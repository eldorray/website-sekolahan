<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('forgot password sends a reset link notification', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'guru@sekolah.sch.id']);

    Livewire::test(ForgotPassword::class)
        ->set('email', 'guru@sekolah.sch.id')
        ->call('submit')
        ->assertHasNoErrors();

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

test('forgot password does not leak unknown emails', function () {
    Notification::fake();

    Livewire::test(ForgotPassword::class)
        ->set('email', 'nobody@sekolah.sch.id')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('status', 'Jika email terdaftar, tautan reset password telah dikirim.');

    Notification::assertNothingSent();
});

test('reset password updates the user password with a valid token', function () {
    $user = User::factory()->create([
        'email' => 'admin@sekolah.sch.id',
        'password' => Hash::make('old-password'),
    ]);

    $token = Password::createToken($user);

    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('email', 'admin@sekolah.sch.id')
        ->set('password', 'new-password-123')
        ->set('password_confirmation', 'new-password-123')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    expect(Hash::check('new-password-123', $user->fresh()->password))->toBeTrue();
});

test('reset password rejects an invalid token', function () {
    $user = User::factory()->create([
        'email' => 'admin@sekolah.sch.id',
        'password' => Hash::make('old-password'),
    ]);

    Livewire::test(ResetPassword::class, ['token' => 'invalid-token'])
        ->set('email', 'admin@sekolah.sch.id')
        ->set('password', 'new-password-123')
        ->set('password_confirmation', 'new-password-123')
        ->call('submit')
        ->assertHasErrors('email');

    expect(Hash::check('old-password', $user->fresh()->password))->toBeTrue();
});
