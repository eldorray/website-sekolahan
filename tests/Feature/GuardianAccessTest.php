<?php

use App\Livewire\Auth\TwoFactorChallenge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PragmaRX\Google2FAQRCode\Google2FA;

uses(RefreshDatabase::class);

test('dashboard destination is explicit for every supported role', function () {
    expect((new User(['role' => 'admin']))->dashboardRoute())->toBe('admin.dashboard')
        ->and((new User(['role' => 'guru']))->dashboardRoute())->toBe('guru.dashboard')
        ->and((new User(['role' => 'orang_tua']))->dashboardRoute())->toBe('orang-tua.spp');
});

test('inactive authenticated accounts cannot enter a role protected panel', function () {
    $user = User::factory()->create(['role' => 'admin', 'is_active' => false]);

    $this->actingAs($user)->get('/admin')->assertForbidden();
});

test('two factor guardian login redirects to the tuition portal', function () {
    $user = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $user->generateTwoFactorSecret();
    $code = app(Google2FA::class)->getCurrentOtp($user->twoFactorSecret());
    $user->confirmTwoFactor($code);
    session()->put('login.2fa.user_id', $user->id);

    Livewire::test(TwoFactorChallenge::class)
        ->set('code', $code)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('orang-tua.spp'));

    expect(auth()->id())->toBe($user->id);
});

test('an account deactivated during two factor login cannot authenticate', function () {
    $user = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $user->generateTwoFactorSecret();
    $code = app(Google2FA::class)->getCurrentOtp($user->twoFactorSecret());
    $user->confirmTwoFactor($code);
    $user->update(['is_active' => false]);
    session()->put('login.2fa.user_id', $user->id);

    Livewire::test(TwoFactorChallenge::class)
        ->set('code', $code)
        ->call('submit')
        ->assertRedirect(route('login'));

    expect(auth()->check())->toBeFalse()
        ->and(session()->has('login.2fa.user_id'))->toBeFalse();
});
