<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(fn () => RateLimiter::clear('test@school.id|127.0.0.1'));

test('login locks out after 5 failed attempts', function () {
    User::factory()->create([
        'email' => 'test@school.id',
        'password' => bcrypt('correct-password'),
        'is_active' => true,
    ]);

    $component = Livewire::test(Login::class)->set('email', 'test@school.id');

    // 5 wrong attempts → "wrong credentials" each time.
    for ($i = 0; $i < 5; $i++) {
        $component->set('password', 'wrong')->call('submit');
    }

    // 6th attempt is rate limited, even with the correct password.
    $component->set('password', 'correct-password')->call('submit');

    $errors = $component->errors()->get('email');
    expect($errors[0])->toContain('Terlalu banyak percobaan');

    // Confirm the user was NOT logged in despite correct password on attempt 6.
    expect(auth()->check())->toBeFalse();
});

test('attempts below the threshold are not locked out', function () {
    User::factory()->create([
        'email' => 'test@school.id',
        'password' => bcrypt('correct-password'),
        'is_active' => true,
    ]);

    $component = Livewire::test(Login::class)->set('email', 'test@school.id');

    // 4 wrong attempts (below the 5 limit): still "wrong credentials",
    // never the lockout message.
    for ($i = 0; $i < 4; $i++) {
        $component->set('password', 'wrong')->call('submit');
        expect($component->errors()->get('email')[0])->toBe('Email atau password salah.');
    }

    expect(RateLimiter::attempts('test@school.id|127.0.0.1'))->toBe(4);
});
