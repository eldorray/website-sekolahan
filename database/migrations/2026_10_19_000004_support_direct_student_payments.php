<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spp_payments', function (Blueprint $table) {
            $table->foreignId('spp_invoice_id')->nullable()->change();
            $table->foreignId('student_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('category', 10)->default('spp');
            $table->date('period')->nullable();
            $table->string('active_student_period')->nullable()->unique();
            $table->index(['student_id', 'period']);
        });
        DB::table('spp_payments')->whereNotNull('spp_invoice_id')->update([
            'student_id' => DB::raw('(SELECT student_id FROM spp_invoices WHERE spp_invoices.id = spp_payments.spp_invoice_id)'),
            'period' => DB::raw('(SELECT period FROM spp_invoices WHERE spp_invoices.id = spp_payments.spp_invoice_id)'),
        ]);
    }

    public function down(): void
    {
        // Standalone payments cannot be restored to an invoice-only schema without losing records.
        if (DB::table('spp_payments')->whereNull('spp_invoice_id')->exists()) {
            throw new RuntimeException('Arsip pembayaran langsung masih ada; rollback dibatalkan agar riwayat tidak hilang.');
        }
        Schema::table('spp_payments', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'period']);
            $table->dropUnique(['active_student_period']);
            $table->dropConstrainedForeignId('student_id');
            $table->dropColumn(['category', 'period', 'active_student_period']);
            $table->foreignId('spp_invoice_id')->nullable(false)->change();
        });
    }
};
