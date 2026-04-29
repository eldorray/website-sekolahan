<?php

namespace App\Livewire\Concerns;

trait WithDeleteConfirm
{
    public ?int $confirmingDeleteId = null;
    public string $confirmingDeleteLabel = '';

    public function confirmDelete(int $id, string $label = ''): void
    {
        $this->confirmingDeleteId = $id;
        $this->confirmingDeleteLabel = $label;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
        $this->confirmingDeleteLabel = '';
    }

    public function confirmDestroy(): void
    {
        if ($this->confirmingDeleteId === null) return;
        $id = $this->confirmingDeleteId;
        $this->cancelDelete();
        $this->delete($id);
    }
}
