<?php

namespace App\Livewire\Auth;

use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.panel')]
class TwoFactorSettings extends Component
{
    public bool $enrolling = false;

    public ?string $qrCode = null;

    public ?string $secret = null;

    public array $recoveryCodes = [];

    public string $confirmCode = '';

    public function startEnroll(): void
    {
        $user = auth()->user();
        $user->generateTwoFactorSecret();

        $this->secret = $user->twoFactorSecret();
        $this->qrCode = $user->twoFactorQrCode();
        $this->recoveryCodes = $user->twoFactorRecoveryCodes();
        $this->enrolling = true;
    }

    public function confirm(): void
    {
        $this->validate(['confirmCode' => 'required|string']);

        if (! auth()->user()->confirmTwoFactor($this->confirmCode)) {
            throw ValidationException::withMessages(['confirmCode' => 'Kode salah. Coba lagi.']);
        }

        $this->reset(['enrolling', 'qrCode', 'secret', 'confirmCode']);
        session()->flash('status', 'Autentikasi dua faktor aktif.');
    }

    public function disable(): void
    {
        auth()->user()->disableTwoFactor();
        $this->reset(['enrolling', 'qrCode', 'secret', 'recoveryCodes', 'confirmCode']);
        session()->flash('status', 'Autentikasi dua faktor dinonaktifkan.');
    }

    public function render()
    {
        return view('livewire.auth.two-factor-settings', [
            'enabled' => auth()->user()->hasTwoFactorEnabled(),
        ])->layout('layouts.panel', ['title' => 'Keamanan (2FA)']);
    }
}
