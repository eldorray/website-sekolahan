<?php

namespace App\Services;

use App\Models\Student;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

class SppBilling
{
    public function generate(string $period, ?int $studentId = null): int
    {
        if (! preg_match('/\A[1-9][0-9]{3}-(0[1-9]|1[0-2])\z/', $period)) {
            throw ValidationException::withMessages(['period' => 'Periode harus berformat YYYY-MM.']);
        }
        $month = CarbonImmutable::createFromFormat('!Y-m', $period);
        $count = 0;
        foreach (Student::query()->where('is_active', true)->whereDate('billing_start', '<=', $month->endOfMonth())->when($studentId !== null, fn ($query) => $query->whereKey($studentId))->cursor() as $student) {
            $invoice = $student->invoices()->firstOrCreate(['period' => $month], [
                'due_date' => $month->day(max(1, min((int) config('spp.due_day', 10), $month->daysInMonth))),
                'base_amount' => $student->monthly_fee,
                'discount_amount' => $student->discount_amount,
                'amount' => $student->monthly_fee - $student->discount_amount,
                'status' => $student->monthly_fee === $student->discount_amount ? 'waived' : 'unpaid',
            ]);
            $count += (int) $invoice->wasRecentlyCreated;
        }

        return $count;
    }
}
