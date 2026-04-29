<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\PpdbRegistration;
use Livewire\Component;
use Livewire\WithPagination;

class Ppdb extends Component
{
    use WithPagination, WithNotifications, WithDeleteConfirm;

    public string $search = '';
    public string $statusFilter = '';
    public ?int $viewing = null;

    public function setStatus(int $id, string $status): void
    {
        PpdbRegistration::find($id)->update(['status' => $status]);
        $labels = ['accepted' => 'diterima', 'rejected' => 'ditolak', 'pending' => 'pending'];
        $this->notifySuccess('Pendaftaran ' . ($labels[$status] ?? $status) . '.');
    }

    public function delete(int $id): void
    {
        PpdbRegistration::findOrFail($id)->delete();
        $this->notifySuccess('Data pendaftaran berhasil dihapus.');
    }

    public function render()
    {
        $items = PpdbRegistration::query()
            ->when($this->search, fn($q) => $q->where('full_name', 'like', "%{$this->search}%")->orWhere('registration_number', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()->paginate(15);

        $detail = $this->viewing ? PpdbRegistration::find($this->viewing) : null;

        return view('livewire.admin.ppdb', compact('items', 'detail'))
            ->layout('layouts.panel', ['title' => 'PPDB']);
    }
}
