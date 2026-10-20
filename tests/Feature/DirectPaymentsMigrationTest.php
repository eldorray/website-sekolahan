<?php

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('direct payment migration preserves old invoice payment history and backfills student and period', function () {
    $migration = require database_path('migrations/2026_10_19_000004_support_direct_student_payments.php');
    $migration->down();
    $student = Student::create(['nis' => '000001', 'name' => 'Siswa Arsip', 'class_name' => 'VII', 'monthly_fee' => 250000, 'discount_amount' => 0, 'billing_start' => '2026-01-01', 'is_active' => true]);
    $invoice = $student->invoices()->create(['period' => '2026-10-01', 'due_date' => '2026-10-10', 'base_amount' => 250000, 'discount_amount' => 0, 'amount' => 250000, 'status' => 'paid', 'paid_at' => now()]);
    $id = DB::table('spp_payments')->insertGetId(['spp_invoice_id' => $invoice->id, 'order_id' => 'ARSIP-TEST', 'environment' => 'production', 'amount' => 250000, 'status' => 'approved', 'payment_type' => 'qris_static', 'proof_path' => 'spp/proofs/keep.png', 'paid_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
    $migration->up();
    $payment = DB::table('spp_payments')->find($id);
    expect($payment->student_id)->toBe($student->id)->and(substr($payment->period, 0, 10))->toBe('2026-10-01')
        ->and($payment->category)->toBe('spp')->and($payment->amount)->toBe(250000)
        ->and($payment->status)->toBe('approved')->and($payment->proof_path)->toBe('spp/proofs/keep.png');
});
