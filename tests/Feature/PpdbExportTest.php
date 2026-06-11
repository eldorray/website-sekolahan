<?php

use App\Exports\PpdbCsv;
use App\Livewire\Admin\Ppdb as AdminPpdb;
use App\Models\PpdbRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makeReg(array $overrides = []): PpdbRegistration
{
    return PpdbRegistration::createWithNumber(array_merge([
        'full_name' => 'Anak Uji',
        'gender' => 'L',
        'birthplace' => 'Depok',
        'birthdate' => '2015-01-01',
        'address' => 'Jl. Test',
        'father_name' => 'Ayah',
        'mother_name' => 'Ibu',
        'parent_phone' => '0812',
        'grade_target' => 'SD Kelas 1',
    ], $overrides));
}

test('csv row maps gender label and columns', function () {
    $reg = makeReg(['full_name' => 'Siti', 'gender' => 'P']);

    $row = PpdbCsv::row($reg->fresh());

    expect($row[1])->toBe('Siti')
        ->and($row[3])->toBe('Perempuan')
        ->and($row)->toHaveCount(count(PpdbCsv::HEADER));
});

test('admin can download the ppdb export', function () {
    makeReg();
    makeReg(['full_name' => 'Dua']);
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(AdminPpdb::class)
        ->call('export')
        ->assertFileDownloaded();
});

test('exporter streams only rows matching the query', function () {
    $a = makeReg(['full_name' => 'Diterima']);
    $a->update(['status' => 'accepted']);
    makeReg(['full_name' => 'PendingSaja']);

    $response = PpdbCsv::download(
        PpdbRegistration::where('status', 'accepted'),
        'ppdb.csv'
    );

    ob_start();
    $response->sendContent();
    $csv = ob_get_clean();

    expect($csv)
        ->toContain('Diterima')
        ->toContain('No. Pendaftaran') // header present
        ->not->toContain('PendingSaja');
});
