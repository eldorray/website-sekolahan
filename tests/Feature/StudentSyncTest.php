<?php

use App\Livewire\Admin\Students;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use App\Services\DataIndukStudents;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Cache::flush();
    Http::preventStrayRequests();
});

function dataIndukRow(array $attributes = []): array
{
    return array_replace([
        'id' => 17, 'nisn' => '0012345678', 'nama_lengkap' => 'Siswa Data Induk',
        'tingkat_rombel' => 'Kelas 1 - KELAS 1A', 'status' => 'Aktif',
        'nik' => 'private-nik', 'alamat' => 'Alamat privat', 'no_telepon' => 'private-phone',
    ], $attributes);
}

function dataIndukResponse(array $rows): array
{
    return ['success' => true, 'data' => $rows, 'total' => count($rows)];
}

test('admin student page offers API synchronization for MI and SMP', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Livewire::actingAs($admin)->test(Students::class)->assertSee('Sinkron dari Data Induk')
        ->call('openSync')->assertSet('showSyncModal', true)->assertSee('MI')->assertSee('SMP')
        ->assertSee('URL Data Induk')->assertSee('Tarif siswa baru');
});

test('sync reads the selected API and imports NISN without a guardian or private profile fields', function (string $source, string $endpoint) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Http::fake(['https://induk.example.test/api/'.$endpoint.'/all' => Http::response(dataIndukResponse([dataIndukRow()]))]);

    $result = app(DataIndukStudents::class)->sync($admin, 'https://induk.example.test/', $source, 250000, '2026-10-01');
    $student = Student::sole();
    expect($student->nis)->toBe('0012345678')->and($student->name)->toBe('Siswa Data Induk')
        ->and($student->class_name)->toBe('Kelas 1 - KELAS 1A')->and($student->monthly_fee)->toBe(250000)
        ->and($student->discount_amount)->toBe(0)->and($student->billing_start->toDateString())->toBe('2026-10-01')
        ->and($student->is_active)->toBeTrue()->and($student->data_induk_source)->toBe($source)
        ->and($student->data_induk_id)->toBe(17)->and($student->guardians()->count())->toBe(0)
        ->and($student->getAttributes())->not->toHaveKeys(['nik', 'alamat', 'no_telepon'])
        ->and($result)->toBe(['created' => 1, 'updated' => 0, 'unchanged' => 0, 'skipped' => 0]);
    Http::assertSent(fn (Request $request) => $request->method() === 'GET' && $request->url() === 'https://induk.example.test/api/'.$endpoint.'/all');
})->with([['mi', 'siswa-mi'], ['smp', 'siswa-smp']]);

test('repeat sync preserves billing and guardians while updating corrected identities by stable source id', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $guardian = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    Http::fake(['*' => Http::sequence()
        ->push(dataIndukResponse([dataIndukRow()]))
        ->push(dataIndukResponse([dataIndukRow()]))
        ->push(dataIndukResponse([dataIndukRow(['nisn' => '0098765432', 'nama_lengkap' => 'Nama Diperbaiki', 'tingkat_rombel' => 'Kelas 2', 'status' => 'Pindah'])]))]);
    $sync = app(DataIndukStudents::class);
    $sync->sync($admin, 'https://induk.example.test', 'mi', 250000, '2026-10-01');
    $student = Student::sole();
    $student->update(['discount_amount' => 50000]);
    $student->guardians()->attach($guardian);
    $invoice = $student->invoices()->create([
        'period' => '2026-10-01', 'due_date' => '2026-10-10', 'base_amount' => 250000,
        'discount_amount' => 50000, 'amount' => 200000, 'status' => 'unpaid',
    ]);

    expect($sync->sync($admin, 'https://induk.example.test', 'mi', 999999, '2027-01-01')['unchanged'])->toBe(1);
    expect($sync->sync($admin, 'https://induk.example.test', 'mi', 999999, '2027-01-01')['updated'])->toBe(1);
    expect(Student::count())->toBe(1)->and($student->fresh()->nis)->toBe('0098765432')
        ->and($student->fresh()->name)->toBe('Nama Diperbaiki')->and($student->fresh()->class_name)->toBe('Kelas 2')
        ->and($student->fresh()->is_active)->toBeFalse()->and($student->fresh()->monthly_fee)->toBe(250000)
        ->and($student->fresh()->discount_amount)->toBe(50000)->and($student->fresh()->billing_start->toDateString())->toBe('2026-10-01')
        ->and($student->guardians()->sole()->id)->toBe($guardian->id)->and($invoice->fresh()->amount)->toBe(200000);
});

test('sync links matching existing students and skips missing NISN or conflicting school identities', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $student = Student::create([
        'nis' => '0012345678', 'name' => 'Siswa Data Induk', 'class_name' => 'Kelas Lama',
        'monthly_fee' => 100000, 'discount_amount' => 10000, 'billing_start' => '2026-07-01', 'is_active' => true,
    ]);
    Http::fake(['*' => Http::sequence()->push(dataIndukResponse([dataIndukRow(), dataIndukRow(['id' => 18, 'nisn' => null])]))
        ->push(dataIndukResponse([dataIndukRow(['id' => 200])]))]);
    $sync = app(DataIndukStudents::class);
    expect($sync->sync($admin, 'https://induk.example.test', 'mi', 250000, '2026-10-01'))
        ->toBe(['created' => 0, 'updated' => 1, 'unchanged' => 0, 'skipped' => 1]);
    expect($student->fresh()->data_induk_id)->toBe(17)->and($student->fresh()->monthly_fee)->toBe(100000);
    expect($sync->sync($admin, 'https://induk.example.test', 'smp', 250000, '2026-10-01')['skipped'])->toBe(1);
    expect(Student::count())->toBe(1)->and($student->fresh()->data_induk_source)->toBe('mi');
});

test('sync does not claim an unrelated manual student or steal another students NISN', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $student = Student::create([
        'nis' => '0012345678', 'name' => 'Siswa Berbeda', 'class_name' => 'Kelas Lama',
        'monthly_fee' => 100000, 'discount_amount' => 0, 'billing_start' => '2026-07-01', 'is_active' => true,
    ]);
    Http::fake(['*' => Http::response(dataIndukResponse([dataIndukRow()]))]);
    expect(app(DataIndukStudents::class)->sync($admin, 'https://induk.example.test', 'mi', 250000, '2026-10-01')['skipped'])->toBe(1);
    expect($student->fresh()->name)->toBe('Siswa Berbeda')->and($student->fresh()->data_induk_id)->toBeNull();
});

test('unsuccessful or malformed responses cannot change local students', function (array|string $body, int $status) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Http::fake(['*' => Http::response($body, $status)]);
    expect(fn () => app(DataIndukStudents::class)->sync($admin, 'https://induk.example.test', 'mi', 250000, '2026-10-01'))
        ->toThrow(ValidationException::class);
    expect(Student::count())->toBe(0);
})->with([
    'server error' => [['success' => false], 500],
    'HTML login' => ['<html>Login</html>', 200],
    'missing data' => [['success' => true], 200],
    'duplicate id' => [dataIndukResponse([dataIndukRow(), dataIndukRow(['nisn' => '0000000002'])]), 200],
]);

test('a failed connection has a usable message and does not save connection settings', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Http::fake(['*' => Http::failedConnection()]);
    Livewire::actingAs($admin)->test(Students::class)->call('openSync')
        ->set('syncUrl', 'https://induk.example.test')->set('syncSource', 'mi')
        ->set('syncMonthlyFee', 250000)->set('syncBillingStart', '2026-10-01')->call('syncStudents')
        ->assertHasErrors('sync')->assertSet('showSyncModal', true)->assertSee('Tidak dapat menghubungi Data Induk');
    $this->assertDatabaseMissing('settings', ['key' => 'data_induk_url']);
});

test('MI and SMP source ids do not collide and missing remote rows never delete local history', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Http::fake(['*' => Http::sequence()->push(dataIndukResponse([dataIndukRow()]))
        ->push(dataIndukResponse([dataIndukRow(['nisn' => '0099999999', 'nama_lengkap' => 'Siswa SMP'])]))
        ->push(dataIndukResponse([]))]);
    $sync = app(DataIndukStudents::class);
    $sync->sync($admin, 'https://induk.example.test', 'mi', 250000, '2026-10-01');
    $sync->sync($admin, 'https://induk.example.test', 'smp', 350000, '2026-10-01');
    expect(Student::count())->toBe(2)->and(Student::where('data_induk_source', 'smp')->sole()->data_induk_id)->toBe(17);
    expect($sync->sync($admin, 'https://induk.example.test', 'mi', 250000, '2026-10-01'))
        ->toBe(['created' => 0, 'updated' => 0, 'unchanged' => 0, 'skipped' => 0]);
    expect(Student::count())->toBe(2)->and(Student::where('is_active', true)->count())->toBe(2);
});

test('a different Data Induk server cannot replace an already linked student identity', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Http::fake(['*' => Http::sequence()->push(dataIndukResponse([dataIndukRow()]))
        ->push(dataIndukResponse([dataIndukRow(['nisn' => '0099999999', 'nama_lengkap' => 'Anak Server Lain'])]))]);
    $sync = app(DataIndukStudents::class);
    $sync->sync($admin, 'https://induk.example.test', 'mi', 250000, '2026-10-01');
    expect(fn () => $sync->sync($admin, 'https://server-lain.example.test', 'mi', 250000, '2026-10-01'))
        ->toThrow(ValidationException::class);
    expect(Student::sole()->nis)->toBe('0012345678')->and(Student::sole()->name)->toBe('Siswa Data Induk')
        ->and(Setting::get('data_induk_url'))->toBe('https://induk.example.test');
});

test('a graduate can move from MI to SMP without duplicating history or being deactivated by the old school', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Http::fake(['*' => Http::sequence()
        ->push(dataIndukResponse([dataIndukRow(['status' => 'Lulus'])]))
        ->push(dataIndukResponse([dataIndukRow(['id' => 88, 'tingkat_rombel' => 'Kelas 7'])]))
        ->push(dataIndukResponse([dataIndukRow(['status' => 'Lulus'])]))]);
    $sync = app(DataIndukStudents::class);
    $sync->sync($admin, 'https://induk.example.test', 'mi', 250000, '2026-10-01');
    $student = Student::sole();
    $guardian = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $student->guardians()->attach($guardian);
    $invoice = $student->invoices()->create([
        'period' => '2026-10-01', 'due_date' => '2026-10-10', 'base_amount' => 250000,
        'discount_amount' => 0, 'amount' => 250000, 'status' => 'unpaid',
    ]);
    expect($sync->sync($admin, 'https://induk.example.test', 'smp', 350000, '2026-10-01')['updated'])->toBe(1);
    expect(Student::count())->toBe(1)->and($student->fresh()->is_active)->toBeTrue()
        ->and($student->fresh()->data_induk_source)->toBe('smp')->and($student->fresh()->data_induk_id)->toBe(88)
        ->and($student->fresh()->monthly_fee)->toBe(250000)->and($invoice->fresh()->student_id)->toBe($student->id)
        ->and($student->guardians()->sole()->id)->toBe($guardian->id);
    expect($sync->sync($admin, 'https://induk.example.test', 'mi', 250000, '2026-10-01')['skipped'])->toBe(1);
    expect($student->fresh()->data_induk_source)->toBe('smp')->and($student->fresh()->is_active)->toBeTrue();
});

test('invalid rows are reported as skipped without inventing identifiers or truncating names', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Http::fake(['*' => Http::response(dataIndukResponse([
        dataIndukRow(['id' => 1, 'nisn' => null]),
        dataIndukRow(['id' => 2, 'nisn' => '0000000002', 'nama_lengkap' => str_repeat('a', 161)]),
        dataIndukRow(['id' => 3, 'nisn' => '0000000003', 'status' => 'unknown']),
        dataIndukRow(['id' => 4, 'nisn' => '0000000004', 'tingkat_rombel' => null]),
    ]))]);
    expect(app(DataIndukStudents::class)->sync($admin, 'https://induk.example.test', 'mi', 250000, '2026-10-01')['skipped'])->toBe(3);
    expect(Student::sole()->nis)->toBe('0000000004')->and(Student::sole()->class_name)->toBe('Belum diatur');
});

test('admin sync dialog saves connection settings and shows an import summary', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Http::fake(['*' => Http::response(dataIndukResponse([dataIndukRow()]))]);
    Livewire::actingAs($admin)->test(Students::class)->call('openSync')
        ->set('syncUrl', 'https://induk.example.test')->set('syncSource', 'smp')
        ->set('syncMonthlyFee', 250000)->set('syncBillingStart', '2026-10-01')->call('syncStudents')
        ->assertHasNoErrors()->assertSet('showSyncModal', false)->assertSee('1 ditambahkan')
        ->assertSee('Siswa Data Induk')->call('openSync')->assertSet('syncSource', 'smp')
        ->assertSet('syncUrl', 'https://induk.example.test');
    expect(Setting::get('data_induk_url'))->toBe('https://induk.example.test');
});

test('sync validates the source and explicit tariff before contacting the API', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Livewire::actingAs($admin)->test(Students::class)->call('openSync')
        ->set('syncUrl', 'ftp://induk.example.test')->set('syncSource', 'unknown')->call('syncStudents')
        ->assertHasErrors(['syncUrl', 'syncSource', 'syncMonthlyFee']);
    Http::assertNothingSent();
});

test('sync is forbidden after an administrator is deactivated including while the API request is running', function (bool $duringRequest) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Http::fake(function () use ($admin, $duringRequest) {
        if ($duringRequest) {
            $admin->update(['is_active' => false]);
        }

        return Http::response(dataIndukResponse([dataIndukRow()]));
    });
    if (! $duringRequest) {
        $admin->update(['is_active' => false]);
    }
    expect(fn () => app(DataIndukStudents::class)->sync($admin, 'https://induk.example.test', 'mi', 250000, '2026-10-01'))
        ->toThrow(HttpException::class);
    expect(Student::count())->toBe(0);
})->with([false, true]);
