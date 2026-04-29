<?php

namespace App\Livewire\Concerns;

trait WithNotifications
{
    public function notify(string $type, string $message): void
    {
        $this->dispatch('toast', type: $type, message: $message);
    }

    public function notifySuccess(string $message): void { $this->notify('success', $message); }
    public function notifyError(string $message): void { $this->notify('error', $message); }
    public function notifyInfo(string $message): void { $this->notify('info', $message); }
}
