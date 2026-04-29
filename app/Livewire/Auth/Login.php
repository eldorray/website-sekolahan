<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function submit()
    {
        $credentials = $this->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, $this->remember)) {
            throw ValidationException::withMessages(['email' => 'Email atau password salah.']);
        }

        $user = Auth::user();
        if (! $user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages(['email' => 'Akun tidak aktif.']);
        }

        request()->session()->regenerate();

        return $this->redirectIntended($user->isAdmin() ? route('admin.dashboard') : route('guru.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.auth');
    }
}
