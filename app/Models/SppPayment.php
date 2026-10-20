<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SppPayment extends Model
{
    protected $fillable = ['spp_invoice_id', 'student_id', 'category', 'period', 'active_student_period', 'user_id', 'order_id', 'environment', 'amount', 'status', 'transaction_id', 'payment_type', 'redirect_url', 'expires_at', 'paid_at', 'requires_review', 'active_invoice_id', 'last_checked_at', 'description', 'proof_path', 'confirmed_at', 'reviewed_by', 'reviewed_at', 'review_notes'];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'period' => 'date', 'expires_at' => 'datetime', 'paid_at' => 'datetime', 'requires_review' => 'boolean', 'last_checked_at' => 'datetime', 'confirmed_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (SppPayment $payment) {
            if ($payment->spp_invoice_id && $payment->student_id === null) {
                $invoice = SppInvoice::findOrFail($payment->spp_invoice_id);
                $payment->student_id = $invoice->student_id;
                $payment->period = $invoice->period;
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SppInvoice::class, 'spp_invoice_id');
    }
}
