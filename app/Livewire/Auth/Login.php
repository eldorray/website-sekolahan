<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /** Max failed attempts per email+IP before lockout. */
    protected int $maxAttempts = 5;

    /** Lockout window in seconds. */
    protected int $decaySeconds = 60;

    public function submit()
    {
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($this->throttleKey(), $this->decaySeconds);
            throw ValidationException::withMessages(['email' => 'Email atau password salah.']);
        }

        $user = Auth::user();
        if (! $user->is_active) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey(), $this->decaySeconds);
            throw ValidationException::withMessages(['email' => 'Akun tidak aktif.']);
        }

        RateLimiter::clear($this->throttleKey());
        request()->session()->regenerate();

        return $this->redirectIntended($user->isAdmin() ? route('admin.dashboard') : route('guru.dashboard'), navigate: true);
    }

    /**
     * Throw a lockout message once too many failed attempts pile up
     * for this email + IP combination.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $this->maxAttempts)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.auth');
    }
}
