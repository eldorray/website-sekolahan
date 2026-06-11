<?php

use App\Models\PpdbRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function ppdbData(string $name = 'Anak Uji'): array
{
    return [
        'full_name' => $name,
        'gender' => 'L',
        'birthplace' => 'Depok',
        'birthdate' => '2015-01-01',
        'address' => 'Jl. Test',
        'father_name' => 'Ayah',
        'mother_name' => 'Ibu',
        'parent_phone' => '0812',
        'grade_target' => 'SD Kelas 1',
    ];
}

test('registration numbers are sequential within the year', function () {
    $year = now()->format('Y');

    $a = PpdbRegistration::createWithNumber(ppdbData('A'));
    $b = PpdbRegistration::createWithNumber(ppdbData('B'));
    $c = PpdbRegistration::createWithNumber(ppdbData('C'));

    expect($a->registration_number)->toBe("PPDB-{$year}-0001")
        ->and($b->registration_number)->toBe("PPDB-{$year}-0002")
        ->and($c->registration_number)->toBe("PPDB-{$year}-0003");
});

test('registration numbers stay unique', function () {
    $numbers = collect(range(1, 20))
        ->map(fn ($i) => PpdbRegistration::createWithNumber(ppdbData("Anak {$i}"))->registration_number);

    expect($numbers->unique()->count())->toBe(20);
});

test('next sequence continues after the existing max for the year', function () {
    $year = now()->format('Y');

    // Simulate an existing record from a prior submission.
    PpdbRegistration::create(array_merge(ppdbData('Lama'), [
        'registration_number' => "PPDB-{$year}-0042",
    ]));

    $next = PpdbRegistration::createWithNumber(ppdbData('Baru'));

    expect($next->registration_number)->toBe("PPDB-{$year}-0043");
});
