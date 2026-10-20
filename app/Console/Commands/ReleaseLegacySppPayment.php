<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\SppInvoice;
use App\Models\SppPayment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReleaseLegacySppPayment extends Command
{
    protected $signature = 'spp:release-legacy {payment : ID pembayaran lama} {--admin= : ID admin pemeriksa} {--reason= : Catatan hasil pemeriksaan} {--confirmed-closed-unpaid : Operator memastikan pesanan produksi sudah ditutup dan tidak menerima dana}';

    protected $description = 'Lepas kunci pesanan SPP lama setelah pemeriksaan eksplisit, tanpa melunasi atau menghapus arsip';

    public function handle(): int
    {
        $data = ['payment' => $this->argument('payment'), 'admin' => $this->option('admin'), 'reason' => trim((string) $this->option('reason'))];
        if (Validator::make($data, ['payment' => ['required', 'integer', 'min:1'], 'admin' => ['required', 'integer', 'min:1'], 'reason' => ['required', 'string', 'max:1000']])->fails()) {
            $this->error('Isi ID pembayaran, --admin, dan --reason yang valid.');

            return self::FAILURE;
        }
        try {
            DB::transaction(function () use ($data) {
                $admin = User::find($data['admin']);
                $payment = SppPayment::find($data['payment']);
                if (! $admin || ! $admin->is_active || ! $admin->isAdmin() || ! $payment) {
                    throw ValidationException::withMessages(['payment' => 'Pembayaran atau admin aktif tidak ditemukan.']);
                }
                if ($payment->spp_invoice_id === null || $payment->payment_type === 'qris_static') {
                    throw ValidationException::withMessages(['payment' => 'Hanya arsip pesanan lama yang dapat dilepas.']);
                }
                Student::whereKey($payment->student_id)->lockForUpdate()->firstOrFail();
                $invoice = SppInvoice::query()->lockForUpdate()->findOrFail($payment->spp_invoice_id);
                $payment = SppPayment::query()->lockForUpdate()->findOrFail($payment->id);
                if ($payment->payment_type === 'qris_static' || $invoice->status !== 'unpaid'
                    || $payment->paid_at !== null || in_array($payment->status, ['paid', 'settlement', 'capture', 'refund', 'partial_refund'], true)
                    || $invoice->payments()->whereNotNull('paid_at')->exists()) {
                    throw ValidationException::withMessages(['payment' => 'Hanya pesanan lama tanpa pelunasan yang boleh dilepas.']);
                }
                if ($payment->environment !== 'sandbox' && ! $this->option('confirmed-closed-unpaid')) {
                    throw ValidationException::withMessages(['payment' => 'Pesanan produksi tetap terkunci. Tutup pesanan dan cocokkan mutasi dahulu, lalu gunakan --confirmed-closed-unpaid hanya jika benar-benar tidak menerima dana.']);
                }
                $oldStatus = $payment->status;
                $payment->update(['status' => 'legacy_closed', 'active_invoice_id' => null, 'requires_review' => false,
                    'reviewed_by' => $admin->id, 'reviewed_at' => now(), 'review_notes' => $data['reason']]);
                AuditLog::create(['user_id' => $admin->id, 'event' => 'spp.legacy_closed', 'auditable_type' => SppPayment::class,
                    'auditable_id' => $payment->id, 'changes' => ['old_status' => $oldStatus, 'status' => 'legacy_closed', 'reason' => $data['reason'],
                        'confirmed_closed_unpaid' => (bool) $this->option('confirmed-closed-unpaid')], 'created_at' => now()]);
            });
        } catch (ValidationException $exception) {
            $this->error(collect($exception->errors())->flatten()->implode(' '));

            return self::FAILURE;
        }
        $this->info('Kunci pesanan lama dilepas. Arsip tetap tersimpan; tagihan tidak dilunasi.');

        return self::SUCCESS;
    }
}
