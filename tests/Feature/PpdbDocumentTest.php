<?php

use App\Livewire\Public\PpdbForm;
use App\Models\PpdbRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'honeypot.randomize_name_field_name' => false,
        'honeypot.amount_of_seconds' => 0,
    ]);
});

function ppdbFormWithFiles($component, bool $withFiles = true)
{
    $component
        ->set('full_name', 'Ananda Putra')
        ->set('gender', 'L')
        ->set('birthplace', 'Depok')
        ->set('birthdate', '2015-01-01')
        ->set('address', 'Jl. Mawar No. 1')
        ->set('father_name', 'Bapak A')
        ->set('mother_name', 'Ibu B')
        ->set('parent_phone', '08123456789')
        ->set('grade_target', 'SD Kelas 1');

    if ($withFiles) {
        $component->set('kk_file', UploadedFile::fake()->create('kk.pdf', 100, 'application/pdf'))
            ->set('birth_certificate_file', UploadedFile::fake()->image('akte.jpg'));
    }

    return $component;
}

test('ppdb submission requires KK and birth certificate', function () {
    ppdbFormWithFiles(Livewire::test(PpdbForm::class), withFiles: false)
        ->call('submit')
        ->assertHasErrors(['kk_file' => 'required', 'birth_certificate_file' => 'required']);

    expect(PpdbRegistration::count())->toBe(0);
});

test('ppdb submission stores documents on the private disk', function () {
    Storage::fake('local');

    ppdbFormWithFiles(Livewire::test(PpdbForm::class))
        ->call('submit')
        ->assertHasNoErrors();

    $reg = PpdbRegistration::first();

    expect($reg->kk_file)->not->toBeNull()
        ->and($reg->birth_certificate_file)->not->toBeNull();

    Storage::disk('local')->assertExists($reg->kk_file);
    Storage::disk('local')->assertExists($reg->birth_certificate_file);
});

test('ppdb rejects an oversized or wrong-type document', function () {
    ppdbFormWithFiles(Livewire::test(PpdbForm::class), withFiles: false)
        ->set('kk_file', UploadedFile::fake()->create('virus.exe', 100))
        ->set('birth_certificate_file', UploadedFile::fake()->create('big.pdf', 5000)) // > 2MB
        ->call('submit')
        ->assertHasErrors(['kk_file', 'birth_certificate_file']);
});

test('admin can download a ppdb document', function () {
    Storage::fake('local');
    ppdbFormWithFiles(Livewire::test(PpdbForm::class))->call('submit');
    $reg = PpdbRegistration::first();

    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)
        ->get(route('admin.ppdb.document', [$reg->id, 'kk']))
        ->assertOk()
        ->assertDownload();
});

test('guests cannot download ppdb documents', function () {
    Storage::fake('local');
    ppdbFormWithFiles(Livewire::test(PpdbForm::class))->call('submit');
    $reg = PpdbRegistration::first();

    $this->get(route('admin.ppdb.document', [$reg->id, 'kk']))
        ->assertRedirect(route('login'));
});

test('guru cannot download ppdb documents', function () {
    Storage::fake('local');
    ppdbFormWithFiles(Livewire::test(PpdbForm::class))->call('submit');
    $reg = PpdbRegistration::first();

    $guru = User::factory()->create(['role' => 'guru', 'is_active' => true]);

    $this->actingAs($guru)
        ->get(route('admin.ppdb.document', [$reg->id, 'kk']))
        ->assertForbidden();
});
