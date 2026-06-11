<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\TwoFactorChallenge;
use App\Livewire\Auth\TwoFactorSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PragmaRX\Google2FAQRCode\Google2FA;

uses(RefreshDatabase::class);

function otpFor(User $user): string
{
    return app(Google2FA::class)->getCurrentOtp($user->twoFactorSecret());
}

test('user can enroll and confirm 2FA', function () {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(TwoFactorSettings::class)
        ->call('startEnroll')
        ->assertSet('enrolling', true);

    expect($user->fresh()->two_factor_secret)->not->toBeNull()
        ->and($user->fresh()->hasTwoFactorEnabled())->toBeFalse(); // not confirmed yet

    $component
        ->set('confirmCode', otpFor($user->fresh()))
        ->call('confirm')
        ->assertHasNoErrors();

    expect($user->fresh()->hasTwoFactorEnabled())->toBeTrue();
});

test('login with 2FA enabled defers to the challenge and does not log in', function () {
    $user = User::factory()->create([
        'email' => 'admin@s.id',
        'password' => Hash::make('secret-pass'),
        'is_active' => true,
    ]);
    $user->generateTwoFactorSecret();
    $user->confirmTwoFactor(otpFor($user));

    Livewire::test(Login::class)
        ->set('email', 'admin@s.id')
        ->set('password', 'secret-pass')
        ->call('submit')
        ->assertRedirect(route('two-factor.challenge'));

    expect(auth()->check())->toBeFalse();
    expect(session('login.2fa.user_id'))->toBe($user->id);
});

test('challenge logs in with a valid code', function () {
    $user = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $user->generateTwoFactorSecret();
    $user->confirmTwoFactor(otpFor($user));

    session()->put('login.2fa.user_id', $user->id);
    session()->put('login.2fa.remember', false);

    Livewire::test(TwoFactorChallenge::class)
        ->set('code', otpFor($user->fresh()))
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.dashboard'));

    expect(auth()->id())->toBe($user->id);
});

test('challenge rejects an invalid code', function () {
    $user = User::factory()->create(['is_active' => true]);
    $user->generateTwoFactorSecret();
    $user->confirmTwoFactor(otpFor($user));

    session()->put('login.2fa.user_id', $user->id);

    Livewire::test(TwoFactorChallenge::class)
        ->set('code', '000000')
        ->call('submit')
        ->assertHasErrors('code');

    expect(auth()->check())->toBeFalse();
});

test('recovery code authenticates and is consumed', function () {
    $user = User::factory()->create();
    $user->generateTwoFactorSecret();
    $user->confirmTwoFactor(otpFor($user));

    $recovery = $user->fresh()->twoFactorRecoveryCodes();
    $code = $recovery[0];

    expect($user->fresh()->verifyTwoFactorCode($code))->toBeTrue()
        ->and($user->fresh()->verifyTwoFactorCode($code))->toBeFalse(); // consumed
});

test('qr code image is generated', function () {
    $user = User::factory()->create();
    $user->generateTwoFactorSecret();

    expect($user->twoFactorQrCode())->toContain('data:image');
});
