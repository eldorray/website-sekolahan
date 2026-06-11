<?php

use App\Livewire\Public\ContactForm;
use App\Livewire\Public\PpdbForm;
use App\Models\ContactMessage;
use App\Models\PpdbRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Deterministic honeypot for tests: fixed field name, no timing window.
    config([
        'honeypot.randomize_name_field_name' => false,
        'honeypot.amount_of_seconds' => 0,
    ]);
    RateLimiter::clear('form:contact|127.0.0.1');
    RateLimiter::clear('form:ppdb|127.0.0.1');
});

function fillContact($component)
{
    return $component
        ->set('name', 'Budi')
        ->set('email', 'budi@example.com')
        ->set('subject', 'Pertanyaan')
        ->set('message', 'Halo, saya ingin bertanya.');
}

test('legitimate contact submission is stored', function () {
    fillContact(Livewire::test(ContactForm::class))
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    expect(ContactMessage::count())->toBe(1);
});

test('filled honeypot blocks the submission', function () {
    fillContact(Livewire::test(ContactForm::class))
        ->set('extraFields.my_name', 'i-am-a-bot')
        ->call('submit')
        ->assertStatus(403);

    expect(ContactMessage::count())->toBe(0);
});

test('contact form is rate limited per IP', function () {
    for ($i = 0; $i < 5; $i++) {
        fillContact(Livewire::test(ContactForm::class))->call('submit');
    }

    fillContact(Livewire::test(ContactForm::class))
        ->call('submit')
        ->assertHasErrors('throttle');

    // The 6th submission was rejected.
    expect(ContactMessage::count())->toBe(5);
});

test('legitimate ppdb submission is stored', function () {
    Storage::fake('local');

    Livewire::test(PpdbForm::class)
        ->set('full_name', 'Ananda Putra')
        ->set('gender', 'L')
        ->set('birthplace', 'Depok')
        ->set('birthdate', '2015-01-01')
        ->set('address', 'Jl. Mawar No. 1')
        ->set('father_name', 'Bapak A')
        ->set('mother_name', 'Ibu B')
        ->set('parent_phone', '08123456789')
        ->set('grade_target', 'SD Kelas 1')
        ->set('kk_file', UploadedFile::fake()->create('kk.pdf', 100, 'application/pdf'))
        ->set('birth_certificate_file', UploadedFile::fake()->image('akte.jpg'))
        ->call('submit')
        ->assertHasNoErrors();

    expect(PpdbRegistration::count())->toBe(1);
});
