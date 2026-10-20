<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithNotifications;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use App\Services\DataIndukStudents;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class Students extends Component
{
    use WithNotifications, WithPagination;

    #[Locked]
    public ?int $editingId = null;

    public bool $showModal = false;

    public string $search = '';

    public string $nis = '';

    public string $name = '';

    public string $class_name = '';

    public $monthly_fee = 0;

    public $discount_amount = 0;

    public string $billing_start = '';

    public bool $is_active = true;

    public array $guardianIds = [];

    #[Locked]
    public bool $hasUnavailableGuardians = false;

    public bool $showSyncModal = false;

    public string $syncUrl = '';

    public string $syncSource = 'mi';

    public $syncMonthlyFee = '';

    public string $syncBillingStart = '';

    #[Locked]
    public string $syncSummary = '';

    public function boot(): void
    {
        $this->authorizeAdmin();
    }

    public function create(): void
    {
        $this->authorizeAdmin();
        $this->resetForm();
        $this->billing_start = now()->startOfMonth()->toDateString();
        $this->showModal = true;
    }

    public function openSync(): void
    {
        $this->authorizeAdmin();
        $this->resetValidation();
        $this->syncUrl = Setting::get('data_induk_url', '');
        $this->syncSource = Setting::get('data_induk_source', 'mi');
        $this->syncBillingStart = now()->startOfMonth()->toDateString();
        $this->reset('syncMonthlyFee');
        $this->showSyncModal = true;
    }

    public function closeSync(): void
    {
        $this->authorizeAdmin();
        $this->showSyncModal = false;
        $this->resetValidation();
    }

    public function syncStudents(): void
    {
        $admin = $this->authorizeAdmin();
        $this->showSyncModal = true;
        $this->syncUrl = rtrim(trim($this->syncUrl), '/');
        $this->validate([
            'syncUrl' => ['required', 'url:http,https', 'max:2000'],
            'syncSource' => ['required', Rule::in(['mi', 'smp'])],
            'syncMonthlyFee' => ['required', 'integer', 'min:0', 'max:100000000'],
            'syncBillingStart' => ['required', 'date_format:Y-m-d'],
        ], [
            'syncUrl.required' => 'Masukkan URL Data Induk.',
            'syncUrl.url' => 'Gunakan URL Data Induk dengan http atau https.',
            'syncSource.in' => 'Pilih sumber MI atau SMP.',
            'syncMonthlyFee.required' => 'Isi tarif SPP untuk siswa baru. Gunakan 0 hanya untuk bebas SPP.',
            'syncMonthlyFee.integer' => 'Tarif harus berupa rupiah bulat tanpa desimal.',
            'syncMonthlyFee.min' => 'Tarif SPP tidak boleh negatif.',
            'syncMonthlyFee.max' => 'Tarif SPP maksimal Rp 100.000.000.',
            'syncBillingStart.date_format' => 'Pilih tanggal awal penagihan yang valid.',
        ]);
        $result = app(DataIndukStudents::class)->sync($admin, $this->syncUrl, $this->syncSource, (int) $this->syncMonthlyFee, $this->syncBillingStart);
        $this->syncSummary = strtoupper($this->syncSource).": {$result['created']} ditambahkan, {$result['updated']} diperbarui, {$result['unchanged']} tetap, {$result['skipped']} dilewati.";
        $this->resetPage();
        $this->closeSync();
        $this->notifySuccess($this->syncSummary);
    }

    public function edit(int $id): void
    {
        $this->authorizeAdmin();
        $student = Student::with('guardians')->findOrFail($id);
        $this->resetForm();
        $this->editingId = $student->id;
        $this->nis = $student->nis;
        $this->name = $student->name;
        $this->class_name = $student->class_name;
        $this->monthly_fee = $student->monthly_fee;
        $this->discount_amount = $student->discount_amount;
        $this->billing_start = $student->billing_start->toDateString();
        $this->is_active = $student->is_active;
        $availableGuardians = $student->guardians->filter(fn (User $user) => $user->role === 'orang_tua' && $user->is_active);
        $this->guardianIds = $availableGuardians->modelKeys();
        $this->hasUnavailableGuardians = $availableGuardians->count() !== $student->guardians->count();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorizeAdmin();
        $this->showModal = true;
        $data = $this->validate([
            'nis' => ['required', 'string', 'max:50', Rule::unique('students', 'nis')->ignore($this->editingId)],
            'name' => ['required', 'string', 'max:160'],
            'class_name' => ['required', 'string', 'max:80'],
            'monthly_fee' => ['required', 'integer', 'min:0', 'max:100000000'],
            'discount_amount' => ['required', 'integer', 'min:0', 'lte:monthly_fee'],
            'billing_start' => ['required', 'date_format:Y-m-d'],
            'is_active' => ['required', 'boolean'],
            'guardianIds' => ['array', 'max:20'],
            'guardianIds.*' => ['required', 'integer', 'distinct', Rule::exists('users', 'id')->where('role', 'orang_tua')->where('is_active', true)],
        ], [
            'discount_amount.lte' => 'Potongan tidak boleh melebihi tarif bulanan.',
            'guardianIds.*.exists' => 'Pilih akun orang tua yang masih aktif.',
            'monthly_fee.integer' => 'Tarif harus berupa rupiah bulat tanpa desimal.',
        ]);

        $guardianIds = $data['guardianIds'];
        unset($data['guardianIds']);

        DB::transaction(function () use ($data, $guardianIds) {
            $guardians = User::whereIn('id', $guardianIds)->where('role', 'orang_tua')
                ->where('is_active', true)->lockForUpdate()->get();
            if ($guardians->count() !== count($guardianIds)) {
                throw ValidationException::withMessages(['guardianIds' => 'Akun orang tua berubah. Pilih kembali akun yang aktif.']);
            }

            if ($this->editingId !== null) {
                $student = Student::lockForUpdate()->findOrFail($this->editingId);
                $student->update($data);
            } else {
                $student = Student::create($data);
            }
            $student->guardians()->sync($guardians->modelKeys());
        });

        $this->closeModal();
        $this->notifySuccess('Data siswa berhasil disimpan.');
    }

    public function closeModal(): void
    {
        $this->authorizeAdmin();
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'nis', 'name', 'class_name', 'monthly_fee', 'discount_amount', 'billing_start', 'is_active', 'guardianIds', 'hasUnavailableGuardians']);
        $this->resetValidation();
    }

    private function authorizeAdmin(): User
    {
        $user = auth()->user()?->fresh();
        abort_unless($user && $user->is_active && $user->isAdmin(), 403);

        return $user;
    }

    public function updatedSearch(): void
    {
        $this->authorizeAdmin();
        $this->resetPage();
    }

    public function render()
    {
        $this->authorizeAdmin();
        $items = Student::with('guardians:id,name,email')
            ->when(trim($this->search) !== '', fn ($query) => $query->where(function ($query) {
                $search = '%'.trim($this->search).'%';
                $query->where('name', 'like', $search)->orWhere('nis', 'like', $search)->orWhere('class_name', 'like', $search);
            }))->orderBy('name')->orderBy('id')->paginate(15);
        $guardians = User::where('role', 'orang_tua')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'email']);

        return view('livewire.admin.students', compact('items', 'guardians'))
            ->layout('layouts.panel', ['title' => 'Siswa']);
    }
}
