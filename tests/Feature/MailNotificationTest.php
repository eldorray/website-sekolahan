<?php

use App\Livewire\Public\ContactForm;
use App\Livewire\Public\PpdbForm;
use App\Mail\ContactAdminAlertMail;
use App\Mail\PpdbAdminAlertMail;
use App\Mail\PpdbReceivedMail;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'honeypot.randomize_name_field_name' => false,
        'honeypot.amount_of_seconds' => 0,
    ]);
    Setting::set('email', 'admin@sekolah.sch.id');
});

test('ppdb submission queues registrant confirmation and admin alert', function () {
    Mail::fake();
    Storage::fake('local');

    Livewire::test(PpdbForm::class)
        ->set('full_name', 'Ananda')
        ->set('gender', 'L')
        ->set('birthplace', 'Depok')
        ->set('birthdate', '2015-01-01')
        ->set('address', 'Jl. Test')
        ->set('father_name', 'Ayah')
        ->set('mother_name', 'Ibu')
        ->set('parent_phone', '0812')
        ->set('parent_email', 'ortu@example.com')
        ->set('grade_target', 'SD Kelas 1')
        ->set('kk_file', UploadedFile::fake()->create('kk.pdf', 100, 'application/pdf'))
        ->set('birth_certificate_file', UploadedFile::fake()->image('akte.jpg'))
        ->call('submit')
        ->assertHasNoErrors();

    Mail::assertQueued(PpdbReceivedMail::class, fn ($m) => $m->hasTo('ortu@example.com'));
    Mail::assertQueued(PpdbAdminAlertMail::class, fn ($m) => $m->hasTo('admin@sekolah.sch.id'));
});

test('contact submission queues an admin alert', function () {
    Mail::fake();

    Livewire::test(ContactForm::class)
        ->set('name', 'Budi')
        ->set('email', 'budi@example.com')
        ->set('subject', 'Halo')
        ->set('message', 'Pesan uji')
        ->call('submit')
        ->assertHasNoErrors();

    Mail::assertQueued(ContactAdminAlertMail::class, fn ($m) => $m->hasTo('admin@sekolah.sch.id'));
});
