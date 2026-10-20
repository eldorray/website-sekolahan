<?php

namespace App\Livewire\Public;

use App\Models\Student;
use App\Services\SppPayments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.public')]
class Spp extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    #[Locked]
    public array $matchedStudentIds = [];

    #[Locked]
    public ?int $studentId = null;

    public string $category = '';

    public string $period = '';

    public string $description = '';

    public mixed $amount = null;

    public mixed $proof = null;

    #[Locked]
    public string $paymentStep = 'details';

    #[Locked]
    public array $paymentDetails = [];

    #[Locked]
    public string $confirmationKey = '';

    #[Locked]
    public string $confirmationReference = '';

    public function findStudent(): void
    {
        $this->resetSelection();
        $key = 'spp-lookup:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            throw ValidationException::withMessages(['search' => 'Terlalu banyak pencarian. Tunggu satu menit lalu coba kembali.']);
        }
        RateLimiter::hit($key, 60);
        $this->search = Str::squish($this->search);
        $this->validate(['search' => ['required', 'string', 'min:3', 'max:160']], [
            'search.required' => 'Masukkan NISN atau nama lengkap siswa.',
            'search.min' => 'Masukkan sedikitnya 3 karakter.', 'search.max' => 'Pencarian maksimal 160 karakter.',
        ]);
        $this->matchedStudentIds = $this->matchingStudents()->limit(10)->pluck('id')->all();
        if ($this->matchedStudentIds === []) {
            throw ValidationException::withMessages(['search' => 'Siswa tidak ditemukan. Periksa NISN atau nama lengkapnya.']);
        }
    }

    public function selectStudent(int $id): void
    {
        abort_unless(in_array($id, $this->matchedStudentIds, true) && $this->matchingStudents()->whereKey($id)->exists(), 404);
        $this->backToDetails();
        $this->reset('category', 'description', 'amount', 'confirmationReference');
        $this->studentId = $id;
        $this->period = now()->format('Y-m');
        $this->resetPage();
    }

    public function changeStudent(): void
    {
        $this->resetSelection();
        $this->reset('search');
    }

    private function resetSelection(): void
    {
        $this->reset('matchedStudentIds', 'studentId', 'category', 'period', 'description', 'amount', 'proof', 'paymentStep', 'paymentDetails', 'confirmationKey', 'confirmationReference');
        $this->resetValidation();
        $this->resetPage();
    }

    public function updated(string $property): void
    {
        if ($property === 'search') {
            $this->resetSelection();
        } elseif (in_array($property, ['category', 'period', 'description', 'amount'], true)) {
            $this->backToDetails();
        }
    }

    private function matchingStudents(): Builder
    {
        return Student::where('is_active', true)->where(fn ($query) => $query->where('nis', trim($this->search))
            ->orWhereRaw('LOWER(name) = ?', [Str::lower(Str::squish($this->search))]));
    }

    private function selectedStudent(): ?Student
    {
        return $this->studentId ? $this->matchingStudents()->whereKey($this->studentId)->first() : null;
    }

    public function preparePayment(): void
    {
        $this->backToDetails();
        $student = $this->selectedStudent();
        if (! $student) {
            throw ValidationException::withMessages(['search' => 'Cari dan pilih siswa kembali sebelum membayar.']);
        }
        $details = app(SppPayments::class)->details($student, $this->category, $this->category === 'spp' ? $this->period : '', $this->category === 'other' ? $this->description : '', $this->category === 'other' ? $this->amount : null);
        if (! app(SppPayments::class)->configured()) {
            throw ValidationException::withMessages(['payment' => 'QRIS sekolah belum tersedia. Hubungi admin sekolah.']);
        }
        $this->paymentDetails = $details;
        $this->confirmationKey = (string) Str::uuid();
        $this->paymentStep = 'qris';
    }

    public function backToDetails(): void
    {
        $this->reset('paymentStep', 'paymentDetails', 'confirmationKey', 'proof');
        $this->resetValidation();
    }

    public function confirmPayment(): void
    {
        $student = $this->selectedStudent();
        if (! $student || $this->paymentStep !== 'qris' || $this->paymentDetails === []) {
            throw ValidationException::withMessages(['payment' => 'Pilih siswa dan lengkapi rincian pembayaran terlebih dahulu.']);
        }
        $this->validate(['proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']], [
            'proof.required' => 'Unggah bukti pembayaran untuk konfirmasi.',
            'proof.image' => 'Bukti harus berupa gambar JPG, PNG, atau WebP.',
            'proof.mimes' => 'Bukti harus berupa gambar JPG, PNG, atau WebP.', 'proof.max' => 'Bukti maksimal 5 MB.',
        ]);
        $payment = app(SppPayments::class)->confirmStudent($student, $this->paymentDetails, $this->proof, $this->confirmationKey);
        $this->backToDetails();
        $this->reset('category', 'description', 'amount');
        $this->confirmationReference = $payment->order_id;
        $this->resetPage();
    }

    public function render()
    {
        $student = $this->selectedStudent();
        $results = $this->studentId === null ? $this->matchingStudents()->whereIn('id', $this->matchedStudentIds)->get(['id', 'name', 'class_name', 'nis', 'data_induk_source']) : collect();
        $history = $student ? $student->payments()->latest('id')->paginate(10) : null;
        $configured = app(SppPayments::class)->configured();

        return view('livewire.public.spp', compact('student', 'results', 'history', 'configured'))->title('Pembayaran sekolah tanpa login');
    }
}
