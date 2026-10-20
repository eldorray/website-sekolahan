<?php

use App\Models\SppInvoice;
use App\Models\Student;
use App\Services\SppBilling;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('clamps a configured due day to the valid days of the invoice month', function (int $day, string $expected) {
    config(['spp.due_day' => $day]);
    $student = billingStudent();
    app(SppBilling::class)->generate('2026-02');
    expect($student->invoices()->sole()->due_date->format('Y-m-d'))->toBe($expected);
})->with([[31, '2026-02-28'], [0, '2026-02-01']]);

it('retains the legacy billing command without automatic invoice generation', function () {
    $student = billingStudent();
    $this->artisan('spp:generate', ['--period' => '2026-02', '--student' => $student->id])->assertSuccessful();
    expect($student->invoices()->count())->toBe(1);
    $this->artisan('spp:generate', ['--period' => '2026-02', '--student' => $student->id])->assertSuccessful();
    expect($student->invoices()->count())->toBe(1);
    $events = collect(app(Schedule::class)->events());
    expect($events->contains(fn ($event) => str_contains($event->command ?? '', 'spp:generate')))->toBeFalse()
        ->and($events->contains(fn ($event) => str_contains($event->command ?? '', 'spp:reconcile')))->toBeFalse();
});

it('rejects invalid discounts and negative fees at the domain boundary', function (array $attributes) {
    expect(fn () => billingStudent($attributes))->toThrow(ValidationException::class);
})->with([[['discount_amount' => 250001]], [['monthly_fee' => -1]], [['discount_amount' => -1]]]);

it('rejects malformed periods without creating invoices', function (string $period) {
    billingStudent();
    expect(fn () => app(SppBilling::class)->generate($period))->toThrow(ValidationException::class);
    expect(SppInvoice::count())->toBe(0);
})->with(['2026-13', '2026-2', '2026-02-01', 'nonsense']);

it('waives a full scholarship without a payment transaction', function () {
    $student = billingStudent(['discount_amount' => 250000]);
    app(SppBilling::class)->generate('2026-02');
    expect($student->invoices()->sole()->status)->toBe('waived')
        ->and($student->invoices()->sole()->amount)->toBe(0);
});

it('only bills the selected active student after their starting month', function () {
    $selected = billingStudent(['billing_start' => '2026-02-20']);
    billingStudent(['is_active' => false]);
    billingStudent(['billing_start' => '2026-03-01']);
    billingStudent();
    expect(app(SppBilling::class)->generate('2026-02', $selected->id))->toBe(1)
        ->and(SppInvoice::count())->toBe(1);
});

function billingStudent(array $attributes = []): Student
{
    return Student::create(array_replace([
        'nis' => fake()->unique()->numerify('########'),
        'name' => 'Siswa Uji',
        'class_name' => 'VII A',
        'monthly_fee' => 250000,
        'discount_amount' => 50000,
        'billing_start' => '2026-01-01',
        'is_active' => true,
    ], $attributes));
}

it('generates a monthly invoice with snapshotted fees and configured due day', function () {
    config(['spp.due_day' => 10]);
    $student = billingStudent();

    expect(app(SppBilling::class)->generate('2026-02'))->toBe(1);

    $invoice = $student->invoices()->sole();
    expect($invoice->period->format('Y-m-d'))->toBe('2026-02-01')
        ->and($invoice->due_date->format('Y-m-d'))->toBe('2026-02-10')
        ->and($invoice->base_amount)->toBe(250000)
        ->and($invoice->discount_amount)->toBe(50000)
        ->and($invoice->amount)->toBe(200000)
        ->and($invoice->status)->toBe('unpaid')
        ->and($invoice->student->id)->toBe($student->id);
});

it('does not rewrite an existing monthly snapshot on repeated generation', function () {
    $student = billingStudent();
    $billing = app(SppBilling::class);
    expect($billing->generate('2026-02', $student->id))->toBe(1);
    $student->update(['monthly_fee' => 400000]);
    expect($billing->generate('2026-02', $student->id))->toBe(0)
        ->and($student->invoices()->count())->toBe(1)
        ->and($student->invoices()->sole()->amount)->toBe(200000);
});
