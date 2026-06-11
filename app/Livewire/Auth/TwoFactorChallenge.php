<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class TwoFactorChallenge extends Component
{
    public string $code = '';

    public function mount()
    {
        // No pending 2FA login → back to login.
        if (! session()->has('login.2fa.user_id')) {
            return $this->redirectRoute('login', navigate: true);
        }
    }

    public function submit()
    {
        $this->validate(['code' => 'required|string']);

        $userId = session('login.2fa.user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return $this->redirectRoute('login', navigate: true);
        }

        if (! $user->verifyTwoFactorCode($this->code)) {
            throw ValidationException::withMessages(['code' => 'Kode verifikasi salah.']);
        }

        $remember = (bool) session('login.2fa.remember', false);
        session()->forget(['login.2fa.user_id', 'login.2fa.remember']);

        Auth::login($user, $remember);
        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        return $this->redirectIntended($user->isAdmin() ? route('admin.dashboard') : route('guru.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.two-factor-challenge')->layout('layouts.auth');
    }
}
