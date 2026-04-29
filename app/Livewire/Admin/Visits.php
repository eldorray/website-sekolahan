<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\VisitSchedule;
use Livewire\Component;
use Livewire\WithPagination;

class Visits extends Component
{
    use WithPagination, WithNotifications, WithDeleteConfirm;

    public function setStatus(int $id, string $status): void
    {
        VisitSchedule::find($id)->update(['status' => $status]);
        $labels = ['approved' => 'disetujui', 'rejected' => 'ditolak', 'pending' => 'pending'];
        $this->notifySuccess('Permintaan kunjungan ' . ($labels[$status] ?? $status) . '.');
    }

    public function delete(int $id): void
    {
        VisitSchedule::findOrFail($id)->delete();
        $this->notifySuccess('Permintaan kunjungan berhasil dihapus.');
    }

    public function render()
    {
        $items = VisitSchedule::latest()->paginate(15);
        return view('livewire.admin.visits', compact('items'))
            ->layout('layouts.panel', ['title' => 'Permintaan Kunjungan']);
    }
}
