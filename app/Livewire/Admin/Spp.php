<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithNotifications;
use App\Models\Setting;
use App\Models\SppPayment;
use App\Models\User;
use App\Services\SppBilling;
use App\Services\SppPayments;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

class Spp extends Component
{
    use WithFileUploads, WithNotifications, WithPagination;

    public string $month = '';

    public string $status = '';

    public string $search = '';

    public bool $showModal = false;

    public string $generatePeriod = '';

    public $studentId = '';

    public bool $showQrisModal = false;

    public $qrisImage = null;

    public bool $showReviewModal = false;

    #[Locked]
    public ?int $reviewingId = null;

    public string $reviewNotes = '';

    public function boot(): void
    {
        $this->authorizeAdmin();
    }

    public function mount(): void
    {
        $this->authorizeAdmin();
        $this->month = '';
    }

    public function create(): void
    {
        $this->authorizeAdmin();
        $this->resetValidation();
        $this->generatePeriod = $this->month;
        $this->studentId = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorizeAdmin();
        $this->showModal = true;
        $this->validate([
            'generatePeriod' => ['required', 'date_format:Y-m'],
            'studentId' => ['nullable', 'integer', Rule::exists('students', 'id')->where('is_active', true)],
        ], [
            'generatePeriod.date_format' => 'Gunakan bulan yang valid dengan format YYYY-MM.',
            'studentId.exists' => 'Pilih siswa yang masih aktif.',
        ]);

        $count = app(SppBilling::class)->generate($this->generatePeriod, $this->studentId === '' || $this->studentId === null ? null : (int) $this->studentId);
        $this->month = $this->generatePeriod;
        $this->resetPage();
        $this->closeModal();
        $this->notifySuccess($count.' tagihan baru diterbitkan. Tagihan yang sudah ada tidak diubah.');
    }

    public function closeModal(): void
    {
        $this->authorizeAdmin();
        $this->showModal = false;
        $this->reset(['generatePeriod', 'studentId']);
        $this->resetValidation();
    }

    public function editQris(): void
    {
        $this->authorizeAdmin();
        $this->reset('qrisImage');
        $this->resetValidation();
        $this->showQrisModal = true;
    }

    public function saveQris(): void
    {
        $this->authorizeAdmin();
        $this->showQrisModal = true;
        $this->validate([
            'qrisImage' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'qrisImage.required' => 'Pilih gambar QRIS sekolah.',
            'qrisImage.image' => 'QRIS harus berupa gambar JPG, PNG, atau WebP.',
            'qrisImage.mimes' => 'Format gambar QRIS harus JPG, PNG, atau WebP.',
            'qrisImage.max' => 'Ukuran gambar QRIS maksimal 2 MB.',
            'qrisImage.uploaded' => 'Gambar QRIS gagal diunggah. Coba lagi dengan gambar maksimal 2 MB.',
        ]);

        $disk = Storage::disk('local');
        $path = $this->qrisImage->store('spp/qris', 'local');
        if (! is_string($path) || $path === '' || ! $disk->exists($path)) {
            throw ValidationException::withMessages(['qrisImage' => 'Gambar QRIS gagal disimpan. Silakan coba lagi.']);
        }

        try {
            $oldPath = DB::transaction(function () use ($path) {
                $oldPath = Setting::query()->where('key', 'spp_qris_image')->lockForUpdate()->value('value');
                Setting::set('spp_qris_image', $path);

                return $oldPath;
            });
        } catch (Throwable $exception) {
            $disk->delete($path);

            throw $exception;
        }

        if (is_string($oldPath) && $oldPath !== $path && preg_match('/\Aspp\/qris\/[a-zA-Z0-9_-]+\.(?:jpe?g|png|webp)\z/i', $oldPath)) {
            $disk->delete($oldPath);
        }

        $this->closeQrisModal();
        $this->notifySuccess('QRIS disimpan. Pastikan nama merchant benar dan uji pindai sebelum menerima pembayaran.');
    }

    public function closeQrisModal(): void
    {
        $this->authorizeAdmin();
        $this->showQrisModal = false;
        $this->reset('qrisImage');
        $this->resetValidation();
    }

    public function reviewPayment(int $id): void
    {
        $this->authorizeAdmin();
        $payment = SppPayment::with(['student', 'invoice', 'payer', 'reviewer'])->findOrFail($id);
        $this->resetValidation();
        $this->reviewingId = $payment->id;
        $this->reviewNotes = $payment->review_notes ?? '';
        $this->showReviewModal = true;
    }

    public function approvePayment(): void
    {
        $admin = $this->authorizeAdmin();
        $this->validate(['reviewNotes' => ['nullable', 'string', 'max:1000']], [
            'reviewNotes.max' => 'Catatan pemeriksaan maksimal 1.000 karakter.',
        ]);
        app(SppPayments::class)->review($this->paymentForReview(), $admin, true, trim($this->reviewNotes) ?: null);
        $this->resetPage('confirmationsPage');
        $this->closeReviewModal();
        $this->notifySuccess('Pembayaran disetujui dan dicatat pada riwayat siswa.');
    }

    public function rejectPayment(): void
    {
        $admin = $this->authorizeAdmin();
        $this->reviewNotes = trim($this->reviewNotes);
        $this->validate(['reviewNotes' => ['required', 'string', 'max:1000']], [
            'reviewNotes.required' => 'Alasan penolakan wajib diisi agar orang tua dapat memperbaiki konfirmasi.',
            'reviewNotes.max' => 'Catatan pemeriksaan maksimal 1.000 karakter.',
        ]);
        app(SppPayments::class)->review($this->paymentForReview(), $admin, false, $this->reviewNotes);
        $this->resetPage('confirmationsPage');
        $this->closeReviewModal();
        $this->notifySuccess('Konfirmasi ditolak. Orang tua dapat memperbaiki bukti pembayaran.');
    }

    public function closeReviewModal(): void
    {
        $this->authorizeAdmin();
        $this->reset(['showReviewModal', 'reviewingId', 'reviewNotes']);
        $this->resetValidation();
    }

    private function paymentForReview(): SppPayment
    {
        if ($this->reviewingId === null) {
            throw ValidationException::withMessages(['payment' => 'Pilih konfirmasi pembayaran yang akan diperiksa.']);
        }

        return SppPayment::findOrFail($this->reviewingId);
    }

    private function authorizeAdmin(): User
    {
        $user = auth()->user()?->fresh();
        abort_unless($user && $user->is_active && $user->isAdmin(), 403);

        return $user;
    }

    public function updated(string $property): void
    {
        $this->authorizeAdmin();
        if (in_array($property, ['month', 'status', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $this->authorizeAdmin();
        $filtersValid = Validator::make(['month' => $this->month, 'status' => $this->status, 'search' => $this->search], [
            'month' => ['nullable', 'date_format:Y-m'],
            'status' => ['nullable', Rule::in(['pending_review', 'approved', 'rejected', 'paid'])],
            'search' => ['string', 'max:160'],
        ])->passes();
        $query = SppPayment::query()->when(! $filtersValid, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($filtersValid && $this->month !== '', function ($query) {
                $start = CarbonImmutable::createFromFormat('!Y-m', $this->month);
                $query->whereBetween('created_at', [$start->startOfMonth(), $start->endOfMonth()]);
            })
            ->when(trim($this->search) !== '', fn ($query) => $query->whereHas('student', function ($query) {
                $search = '%'.trim($this->search).'%';
                $query->where('name', 'like', $search)->orWhere('nis', 'like', $search)->orWhere('class_name', 'like', $search);
            }));
        $collected = (int) (clone $query)->where('environment', 'production')->whereIn('status', ['approved', 'paid'])->sum('amount');
        $outstanding = (int) (clone $query)->where('status', 'pending_review')->sum('amount');
        $items = $query->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->with(['student', 'payer', 'reviewer'])->latest('id')->paginate(15);
        $qrisConfigured = app(SppPayments::class)->configured();
        $pendingQuery = SppPayment::where('status', 'pending_review')->where('payment_type', 'qris_static')->where('environment', 'production');
        $pendingCount = (clone $pendingQuery)->count();
        $confirmations = $pendingQuery->with(['student', 'payer'])->orderBy('confirmed_at')->orderBy('id')->paginate(10, ['*'], 'confirmationsPage');
        $reviewingPayment = $this->showReviewModal && $this->reviewingId !== null
            ? SppPayment::with(['student', 'invoice', 'payer', 'reviewer'])->findOrFail($this->reviewingId) : null;

        return view('livewire.admin.spp', compact('items', 'collected', 'outstanding', 'qrisConfigured', 'filtersValid', 'pendingCount', 'confirmations', 'reviewingPayment'))
            ->layout('layouts.panel', ['title' => 'Pembayaran Siswa']);
    }
}
