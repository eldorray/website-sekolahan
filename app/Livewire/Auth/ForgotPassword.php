<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';

    public string $status = '';

    public function submit(): void
    {
        $this->validate(['email' => 'required|email']);

        // Always report the same neutral status to avoid leaking which
        // emails exist.
        Password::sendResetLink(['email' => $this->email]);

        $this->status = 'Jika email terdaftar, tautan reset password telah dikirim.';
        $this->reset('email');
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')->layout('layouts.auth');
    }
}
