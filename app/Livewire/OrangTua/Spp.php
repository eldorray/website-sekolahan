<?php

namespace App\Livewire\OrangTua;

use App\Livewire\Concerns\WithNotifications;
use App\Models\SppInvoice;
use App\Models\User;
use App\Services\SppPayments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Spp extends Component
{
    use WithFileUploads, WithNotifications, WithPagination;

    public bool $showPaymentModal = false;

    #[Locked]
    public string $paymentStep = 'details';

    public string $studentId = '';

    public string $invoiceId = '';

    public string $description = '';

    public mixed $proof = null;

    public function mount(): void
    {
        $this->guardian();
    }

    public function openPayment(?int $invoiceId = null): void
    {
        $guardian = $this->guardian();
        $this->closePayment();

        if ($invoiceId !== null) {
            $invoice = $this->invoices($guardian)->findOrFail($invoiceId);
            $this->studentId = (string) $invoice->student_id;
            $this->invoiceId = (string) $invoice->id;
        }

        $this->showPaymentModal = true;
    }

    public function closePayment(): void
    {
        $this->reset('showPaymentModal', 'paymentStep', 'studentId', 'invoiceId', 'description', 'proof');
        $this->resetValidation();
    }

    public function preparePayment(): void
    {
        $guardian = $this->guardian();
        $this->paymentStep = 'details';
        $this->description = trim($this->description);
        $this->validate([
            'studentId' => ['required', 'integer', Rule::exists('student_user', 'student_id')->where('user_id', $guardian->id)],
            'invoiceId' => ['required', 'integer', Rule::exists('spp_invoices', 'id')->where('student_id', $this->studentId)->where('status', 'unpaid')],
            'description' => ['required', 'string', 'max:1000'],
        ], $this->paymentValidationMessages(), ['studentId' => 'anak', 'invoiceId' => 'tagihan', 'description' => 'keterangan pembayaran']);

        $invoice = $this->invoices($guardian)->findOrFail($this->invoiceId);
        if ($invoice->amount <= 0) {
            throw ValidationException::withMessages(['invoiceId' => 'Tagihan ini tidak dapat dibayar.']);
        }
        if ($invoice->payments()->where(function (Builder $query) {
            $query->whereNotNull('active_invoice_id')->orWhereIn('status', ['pending_review', 'approved'])
                ->orWhereNotNull('paid_at')->orWhere('requires_review', true);
        })->exists()) {
            throw ValidationException::withMessages(['payment' => 'Pembayaran sudah tercatat atau sedang diperiksa. Hubungi admin sekolah dan jangan membayar ulang.']);
        }
        if (! app(SppPayments::class)->configured()) {
            throw ValidationException::withMessages(['payment' => 'QRIS sekolah belum tersedia. Hubungi admin sekolah.']);
        }

        $this->showPaymentModal = true;
        $this->paymentStep = 'qris';
    }

    public function confirmPayment(): void
    {
        $guardian = $this->guardian();
        if (! $this->showPaymentModal || $this->paymentStep !== 'qris') {
            throw ValidationException::withMessages(['payment' => 'Rincian pembayaran harus dilengkapi sebelum konfirmasi QRIS.']);
        }
        $this->description = trim($this->description);
        $this->validate([
            'studentId' => ['required', 'integer', Rule::exists('student_user', 'student_id')->where('user_id', $guardian->id)],
            'invoiceId' => ['required', 'integer', Rule::exists('spp_invoices', 'id')->where('student_id', $this->studentId)->where('status', 'unpaid')],
            'description' => ['required', 'string', 'max:1000'],
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], $this->paymentValidationMessages(), ['studentId' => 'anak', 'invoiceId' => 'tagihan', 'description' => 'keterangan pembayaran', 'proof' => 'bukti pembayaran']);

        $invoice = $this->invoices($guardian)->findOrFail($this->invoiceId);
        app(SppPayments::class)->confirm($invoice, $guardian, $this->description, $this->proof);
        $this->closePayment();
        $this->notifySuccess('Konfirmasi pembayaran diterima. Menunggu pemeriksaan admin; tagihan belum dinyatakan lunas.');
    }

    public function updatedStudentId(): void
    {
        $this->reset('invoiceId', 'paymentStep', 'proof');
        $this->resetValidation();
    }

    public function updatedInvoiceId(): void
    {
        $this->reset('paymentStep', 'proof');
        $this->resetValidation();
    }

    private function paymentValidationMessages(): array
    {
        return [
            'studentId.required' => 'Pilih anak yang terhubung dengan akun Anda.',
            'studentId.integer' => 'Pilihan anak tidak valid.',
            'studentId.exists' => 'Anak yang dipilih tidak terhubung dengan akun Anda.',
            'invoiceId.required' => 'Pilih tagihan SPP yang belum lunas.',
            'invoiceId.integer' => 'Pilihan tagihan tidak valid.',
            'invoiceId.exists' => 'Pilih tagihan yang belum lunas milik anak yang dipilih.',
            'description.required' => 'Keterangan pembayaran wajib diisi.',
            'description.string' => 'Keterangan pembayaran harus berupa teks.',
            'description.max' => 'Keterangan pembayaran maksimal 1.000 karakter.',
            'proof.required' => 'Unggah bukti pembayaran sebelum mengirim konfirmasi.',
            'proof.image' => 'Bukti pembayaran harus berupa gambar JPG, PNG, atau WebP.',
            'proof.mimes' => 'Format bukti pembayaran harus JPG, PNG, atau WebP.',
            'proof.max' => 'Ukuran bukti pembayaran maksimal 5 MB.',
            'proof.uploaded' => 'Bukti pembayaran gagal diunggah. Pilih gambar maksimal 5 MB dan coba lagi.',
        ];
    }

    private function guardian(): User
    {
        $user = User::find(auth()->id());
        abort_unless($user && $user->is_active && $user->isOrangTua(), 403);

        return $user;
    }

    private function invoices(User $guardian): Builder
    {
        return SppInvoice::query()->whereHas('student.guardians', fn (Builder $query) => $query->where('users.id', $guardian->id));
    }

    public function render()
    {
        $guardian = $this->guardian();
        $query = $this->invoices($guardian);
        $outstanding = (clone $query)->where('status', 'unpaid')->sum('amount');
        $paid = (clone $query)->where('status', 'paid')->sum('amount');
        $items = (clone $query)->with(['student', 'payments' => fn ($query) => $query->latest('id')])
            ->orderByDesc('period')->orderByDesc('id')->paginate(12);
        $students = $guardian->students()->orderBy('name')->get();
        $configured = app(SppPayments::class)->configured();
        $paymentInvoices = (clone $query)->where('status', 'unpaid')->where('student_id', $this->studentId)->orderBy('period')->get();
        $selectedInvoice = $this->showPaymentModal && $this->paymentStep === 'qris'
            ? $this->invoices($guardian)->with('student')->where('student_id', $this->studentId)->find($this->invoiceId)
            : null;

        return view('livewire.orang-tua.spp', compact('items', 'students', 'outstanding', 'paid', 'configured', 'paymentInvoices', 'selectedInvoice'))
            ->layout('layouts.panel', ['title' => 'SPP Anak Saya']);
    }
}
