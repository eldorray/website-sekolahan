<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\ContactMessage;
use Livewire\Component;
use Livewire\WithPagination;

class Contacts extends Component
{
    use WithPagination, WithNotifications, WithDeleteConfirm;

    public ?int $viewing = null;

    public function view(int $id): void
    {
        $this->viewing = $id;
        ContactMessage::find($id)->update(['is_read' => true]);
    }

    public function delete(int $id): void
    {
        ContactMessage::findOrFail($id)->delete();
        $this->notifySuccess('Pesan berhasil dihapus.');
    }

    public function render()
    {
        $items = ContactMessage::latest()->paginate(15);
        $detail = $this->viewing ? ContactMessage::find($this->viewing) : null;
        return view('livewire.admin.contacts', compact('items', 'detail'))
            ->layout('layouts.panel', ['title' => 'Pesan Masuk']);
    }
}
