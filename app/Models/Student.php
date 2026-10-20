<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Student extends Model
{
    protected $fillable = ['nis', 'name', 'class_name', 'monthly_fee', 'discount_amount', 'billing_start', 'is_active', 'data_induk_source', 'data_induk_id'];

    protected function casts(): array
    {
        return ['monthly_fee' => 'integer', 'discount_amount' => 'integer', 'billing_start' => 'date', 'is_active' => 'boolean', 'data_induk_id' => 'integer'];
    }

    protected static function booted(): void
    {
        static::saving(function (Student $student) {
            if ($student->monthly_fee < 0 || $student->discount_amount < 0 || $student->discount_amount > $student->monthly_fee) {
                throw ValidationException::withMessages(['discount_amount' => 'Potongan harus antara nol dan biaya SPP.']);
            }
        });
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_user');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SppInvoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SppPayment::class);
    }
}
