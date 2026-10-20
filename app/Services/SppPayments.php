<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\SppInvoice;
use App\Models\SppPayment;
use App\Models\Student;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class SppPayments
{
    public function qrisPath(): ?string
    {
        $path = Setting::get('spp_qris_image');

        return is_string($path) && preg_match('~\Aspp/qris/[a-zA-Z0-9_-]+\.(?:png|jpe?g|webp)\z~', $path)
            && Storage::disk('local')->exists($path) ? $path : null;
    }

    public function configured(): bool
    {
        return $this->qrisPath() !== null;
    }

    public function confirm(SppInvoice $invoice, User|Student $payer, string $description, UploadedFile $proof): SppPayment
    {
        $path = null;
        try {
            return DB::transaction(function () use ($invoice, $payer, $description, $proof, &$path) {
                Student::whereKey($invoice->student_id)->lockForUpdate()->firstOrFail();
                $locked = SppInvoice::query()->lockForUpdate()->findOrFail($invoice->id);
                $actor = $payer instanceof User ? User::find($payer->id) : null;
                if ($payer instanceof User) {
                    abort_unless($actor && $actor->is_active && $actor->isOrangTua(), 403);
                    abort_unless($locked->student->guardians()->where('users.id', $actor->id)->exists(), 404);
                } else {
                    $student = Student::find($payer->id);
                    abort_unless($student && $student->is_active, 403);
                    abort_unless($locked->student_id === $student->id, 404);
                }
                $description = trim($description);
                Validator::make(compact('description', 'proof'), [
                    'description' => ['required', 'string', 'max:1000'],
                    'proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                ])->validate();
                if (! $this->configured()) {
                    throw ValidationException::withMessages(['payment' => 'QRIS sekolah belum tersedia. Hubungi admin sekolah.']);
                }
                if ($locked->status !== 'unpaid' || $locked->amount <= 0) {
                    throw ValidationException::withMessages(['payment' => 'Tagihan ini tidak dapat dibayar.']);
                }
                if ($this->periodBlocked($locked->student_id, $locked->period->toDateString()) || $locked->payments()->where(function ($query) use ($locked) {
                    $query->where('active_invoice_id', $locked->id)->orWhereIn('status', ['pending_review', 'approved'])
                        ->orWhereNotNull('paid_at')->orWhere('requires_review', true);
                })->exists()) {
                    throw ValidationException::withMessages(['payment' => 'Pembayaran sudah dikonfirmasi atau sedang diperiksa. Jangan membayar ulang.']);
                }
                $stored = $proof->store('spp/proofs', 'local');
                if (! is_string($stored) || $stored === '') {
                    throw ValidationException::withMessages(['proof' => 'Bukti pembayaran gagal disimpan. Silakan coba lagi.']);
                }
                $path = $stored;
                $payment = $locked->payments()->create([
                    'student_id' => $locked->student_id, 'category' => 'spp', 'period' => $locked->period,
                    'active_student_period' => $locked->student_id.':'.$locked->period->format('Y-m'),
                    'user_id' => $actor?->id,
                    'order_id' => 'SPP-'.Str::uuid(),
                    'environment' => 'production',
                    'amount' => $locked->amount,
                    'status' => 'pending_review',
                    'payment_type' => 'qris_static',
                    'active_invoice_id' => $locked->id,
                    'description' => $description,
                    'proof_path' => $path,
                    'confirmed_at' => now(),
                    'requires_review' => true,
                ]);
                $this->audit($payment, $actor, 'confirmed');

                return $payment;
            }, 1);
        } catch (Throwable $exception) {
            if ($path !== null) {
                Storage::disk('local')->delete($path);
            }
            if ($exception instanceof UniqueConstraintViolationException) {
                throw ValidationException::withMessages(['payment' => 'Konfirmasi lain sedang diproses. Muat ulang tagihan.']);
            }
            throw $exception;
        }
    }

    public function details(Student $student, string $category, string $period, string $description, mixed $amount): array
    {
        $student = $student->fresh();
        abort_unless($student && $student->is_active, 403);
        $description = trim($description);
        Validator::make(compact('category', 'period', 'description', 'amount'), [
            'category' => ['required', Rule::in(['spp', 'other'])],
            'period' => ['required_if:category,spp', 'nullable', 'date_format:Y-m'],
            'description' => ['required_if:category,other', 'string', 'max:1000'],
            'amount' => ['required_if:category,other', 'nullable', 'integer', 'min:1', 'max:100000000'],
        ], [
            'category.required' => 'Pilih jenis pembayaran.', 'category.in' => 'Pilih SPP atau Lainnya.',
            'period.required_if' => 'Pilih bulan SPP.', 'period.date_format' => 'Pilih bulan dan tahun SPP yang valid.',
            'description.required_if' => 'Isi keterangan pembayaran, misalnya bayar seragam.',
            'description.max' => 'Keterangan maksimal 1.000 karakter.',
            'amount.required_if' => 'Isi nominal pembayaran.', 'amount.integer' => 'Nominal harus berupa rupiah bulat.',
            'amount.min' => 'Nominal harus lebih dari nol.', 'amount.max' => 'Nominal maksimal Rp 100.000.000.',
        ])->validate();
        if ($category === 'other') {
            return ['category' => 'other', 'period' => null, 'description' => $description, 'amount' => (int) $amount];
        }
        $month = CarbonImmutable::createFromFormat('!Y-m', $period);
        $invoice = $student->invoices()->whereDate('period', $month->toDateString())->first();
        if (($invoice && $invoice->status !== 'unpaid') || $this->periodBlocked($student->id, $month->toDateString())) {
            throw ValidationException::withMessages(['payment' => 'SPP bulan ini sudah dibayar atau sedang diperiksa. Jangan membayar ulang.']);
        }
        $fee = $invoice ? $invoice->amount : $student->monthly_fee - $student->discount_amount;
        if ($fee <= 0) {
            throw ValidationException::withMessages(['payment' => 'Siswa bebas SPP atau tarif SPP belum diatur. Hubungi admin sekolah.']);
        }

        return ['category' => 'spp', 'period' => $month->toDateString(), 'description' => 'SPP '.$month->translatedFormat('F Y'), 'amount' => $fee];
    }

    public function confirmStudent(Student $student, array $details, UploadedFile $proof, string $key): SppPayment
    {
        Validator::make(['key' => $key], ['key' => ['required', 'uuid']])->validate();
        $path = null;
        try {
            return DB::transaction(function () use ($student, $details, $proof, $key, &$path) {
                $student = Student::whereKey($student->id)->lockForUpdate()->firstOrFail();
                abort_unless($student->is_active, 403);
                $orderId = 'PAY-'.$key;
                $existing = SppPayment::where('order_id', $orderId)->first();
                if ($existing) {
                    abort_unless($existing->student_id === $student->id, 404);

                    return $existing;
                }
                $quote = $this->details($student, $details['category'] ?? '', substr($details['period'] ?? '', 0, 7), $details['description'] ?? '', $details['category'] === 'other' ? ($details['amount'] ?? null) : null);
                if ($quote['amount'] !== ($details['amount'] ?? null)) {
                    throw ValidationException::withMessages(['payment' => 'Nominal SPP berubah. Kembali ke rincian dan periksa nominal sebelum membayar.']);
                }
                Validator::make(['proof' => $proof], ['proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']], [
                    'proof.image' => 'Bukti harus berupa gambar JPG, PNG, atau WebP.',
                    'proof.mimes' => 'Bukti harus berupa gambar JPG, PNG, atau WebP.', 'proof.max' => 'Bukti maksimal 5 MB.',
                ])->validate();
                if (! $this->configured()) {
                    throw ValidationException::withMessages(['payment' => 'QRIS sekolah belum tersedia. Hubungi admin sekolah.']);
                }
                $invoice = $quote['category'] === 'spp' ? $student->invoices()->whereDate('period', $quote['period'])->lockForUpdate()->first() : null;
                $stored = $proof->store('spp/proofs', 'local');
                if (! is_string($stored) || $stored === '') {
                    throw ValidationException::withMessages(['proof' => 'Bukti pembayaran gagal disimpan. Silakan coba lagi.']);
                }
                $path = $stored;
                $payment = SppPayment::create($quote + [
                    'student_id' => $student->id, 'spp_invoice_id' => $invoice?->id,
                    'order_id' => $orderId, 'environment' => 'production', 'status' => 'pending_review',
                    'payment_type' => 'qris_static', 'active_invoice_id' => $invoice?->id,
                    'active_student_period' => $quote['category'] === 'spp' ? $student->id.':'.substr($quote['period'], 0, 7) : null,
                    'proof_path' => $path, 'confirmed_at' => now(), 'requires_review' => true,
                ]);
                $this->audit($payment, null, 'confirmed');

                return $payment;
            });
        } catch (Throwable $exception) {
            if ($path !== null) {
                Storage::disk('local')->delete($path);
            }
            if ($exception instanceof UniqueConstraintViolationException) {
                throw ValidationException::withMessages(['payment' => 'Pembayaran sudah tercatat atau sedang diproses. Jangan membayar ulang.']);
            }
            throw $exception;
        }
    }

    private function periodBlocked(int $studentId, ?string $period, ?int $except = null): bool
    {
        return SppPayment::where('student_id', $studentId)->where('category', 'spp')->whereDate('period', $period)
            ->when($except !== null, fn ($query) => $query->where('id', '!=', $except))
            ->where(fn ($query) => $query->whereNotNull('active_invoice_id')->orWhereNotNull('active_student_period')
                ->orWhereIn('status', ['pending_review', 'approved'])->orWhereNotNull('paid_at')->orWhere('requires_review', true))
            ->exists();
    }

    public function review(SppPayment $payment, User $admin, bool $approve, ?string $notes = null): void
    {
        DB::transaction(function () use ($payment, $admin, $approve, $notes) {
            $actor = User::query()->find($admin->id);
            abort_unless($actor && $actor->is_active && $actor->isAdmin(), 403);
            $snapshot = SppPayment::findOrFail($payment->id);
            Student::whereKey($snapshot->student_id)->lockForUpdate()->firstOrFail();
            $invoice = $snapshot->spp_invoice_id ? SppInvoice::query()->lockForUpdate()->findOrFail($snapshot->spp_invoice_id) : null;
            $locked = SppPayment::query()->lockForUpdate()->findOrFail($payment->id);
            $notes = trim($notes ?? '');
            Validator::make(['reviewNotes' => $notes], [
                'reviewNotes' => [$approve ? 'nullable' : 'required', 'string', 'max:1000'],
            ], ['reviewNotes.required' => 'Alasan penolakan wajib diisi.'])->validate();
            if ($locked->payment_type !== 'qris_static' || $locked->environment !== 'production') {
                throw ValidationException::withMessages(['payment' => 'Transaksi lama tidak dapat diverifikasi melalui alur QRIS statis.']);
            }
            $target = $approve ? 'approved' : 'rejected';
            if ($locked->status === $target) {
                return;
            }
            if ($locked->status !== 'pending_review' || $locked->amount <= 0
                || ($invoice && ($invoice->status !== 'unpaid' || $locked->amount !== $invoice->amount
                    || $locked->student_id !== $invoice->student_id || $locked->active_invoice_id !== $invoice->id
                    || $invoice->payments()->where('id', '!=', $locked->id)->whereNotNull('paid_at')->exists()))
                || ($locked->category === 'spp' && $this->periodBlocked($locked->student_id, $locked->period?->toDateString(), $locked->id))) {
                throw ValidationException::withMessages(['payment' => 'Status atau nominal pembayaran telah berubah. Muat ulang dan periksa kembali.']);
            }
            if ($approve && (! is_string($locked->proof_path) || ! preg_match('~\Aspp/proofs/[a-zA-Z0-9_-]+\.(?:png|jpe?g|webp)\z~', $locked->proof_path)
                || ! Storage::disk('local')->exists($locked->proof_path))) {
                throw ValidationException::withMessages(['payment' => 'Bukti pembayaran tidak tersedia untuk pemeriksaan.']);
            }
            $reviewedAt = now();
            $locked->update([
                'status' => $target, 'reviewed_by' => $actor->id, 'reviewed_at' => $reviewedAt,
                'review_notes' => $notes !== '' ? $notes : null, 'requires_review' => false,
                'active_invoice_id' => null, 'active_student_period' => $approve ? $locked->active_student_period : null,
                'paid_at' => $approve ? $reviewedAt : null,
            ]);
            if ($approve && $invoice) {
                $invoice->update(['status' => 'paid', 'paid_at' => $reviewedAt]);
            }
            $this->audit($locked, $actor, $target);
        });
    }

    private function audit(SppPayment $payment, ?User $actor, string $event): void
    {
        AuditLog::create([
            'user_id' => $actor?->id, 'event' => 'spp.'.$event, 'auditable_type' => SppPayment::class,
            'auditable_id' => $payment->id, 'changes' => ['status' => $payment->status, 'invoice_id' => $payment->spp_invoice_id, 'student_id' => $payment->student_id, 'category' => $payment->category, 'period' => $payment->period?->toDateString()], 'created_at' => now(),
        ]);
    }
}
