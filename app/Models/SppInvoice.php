<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SppInvoice extends Model
{
    protected $fillable = ['student_id', 'period', 'due_date', 'base_amount', 'discount_amount', 'amount', 'status', 'paid_at'];

    protected function casts(): array
    {
        return ['period' => 'date', 'due_date' => 'date', 'base_amount' => 'integer', 'discount_amount' => 'integer', 'amount' => 'integer', 'paid_at' => 'datetime'];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SppPayment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
