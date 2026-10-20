<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->string('name');
            $table->string('class_name');
            $table->unsignedInteger('monthly_fee');
            $table->unsignedInteger('discount_amount')->default(0);
            $table->date('billing_start');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('student_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['student_id', 'user_id']);
        });
        Schema::create('spp_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->date('period');
            $table->date('due_date');
            $table->unsignedInteger('base_amount');
            $table->unsignedInteger('discount_amount');
            $table->unsignedInteger('amount');
            $table->enum('status', ['unpaid', 'paid', 'waived', 'refunded'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'period']);
        });
        Schema::create('spp_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spp_invoice_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_id')->unique();
            $table->enum('environment', ['sandbox', 'production']);
            $table->unsignedInteger('amount');
            $table->string('status');
            $table->string('transaction_id')->nullable()->unique();
            $table->string('payment_type')->nullable();
            $table->text('redirect_url')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->boolean('requires_review')->default(false);
            $table->timestamp('last_checked_at')->nullable()->index();
            // A durable uniqueness fence; cache lock expiry must never permit a second live order.
            $table->foreignId('active_invoice_id')->nullable()->unique()->constrained('spp_invoices')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spp_payments');
        Schema::dropIfExists('spp_invoices');
        Schema::dropIfExists('student_user');
        Schema::dropIfExists('students');
    }
};
