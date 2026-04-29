<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination, WithNotifications, WithDeleteConfirm;

    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'admin';
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:160',
            'email' => 'required|email|max:160|unique:users,email,' . ($this->editingId ?? 'NULL'),
            'password' => $this->editingId ? 'nullable|string|min:6' : 'required|string|min:6',
            'role' => 'required|in:admin,guru',
            'is_active' => 'boolean',
        ];
    }

    public function edit(int $id): void
    {
        $u = User::findOrFail($id);
        $this->editingId = $u->id;
        $this->name = $u->name;
        $this->email = $u->email;
        $this->role = $u->role;
        $this->is_active = (bool) $u->is_active;
        $this->password = '';
    }

    public function save(): void
    {
        $data = $this->validate();
        if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
        else unset($data['password']);

        if ($this->editingId) {
            User::find($this->editingId)->update($data);
            $this->notifySuccess('Pengguna berhasil diperbarui.');
        } else {
            User::create($data);
            $this->notifySuccess('Pengguna baru berhasil dibuat.');
        }
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) {
            $this->notifyError('Tidak bisa menghapus diri sendiri.');
            return;
        }
        User::findOrFail($id)->delete();
        $this->notifySuccess('Pengguna berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'email', 'password']);
        $this->role = 'admin';
        $this->is_active = true;
    }

    public function render()
    {
        $items = User::latest()->paginate(15);
        return view('livewire.admin.users', compact('items'))
            ->layout('layouts.panel', ['title' => 'Pengguna']);
    }
}
