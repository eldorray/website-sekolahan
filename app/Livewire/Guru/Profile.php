<?php

namespace App\Livewire\Guru;

use App\Livewire\Concerns\WithNotifications;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads, WithNotifications;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $position = '';
    public string $bio = '';
    public string $instagram = '';
    public string $facebook = '';
    public string $password = '';
    public $photo_file;

    public function mount(): void
    {
        $u = auth()->user();
        $this->name = $u->name;
        $this->email = $u->email;
        $this->phone = $u->phone ?? '';
        $this->position = $u->position ?? '';
        $this->bio = $u->bio ?? '';
        $this->instagram = $u->instagram ?? '';
        $this->facebook = $u->facebook ?? '';
    }

    public function save(): void
    {
        $u = auth()->user();
        $data = $this->validate([
            'name' => 'required|string|max:160',
            'email' => 'required|email|max:160|unique:users,email,' . $u->id,
            'phone' => 'nullable|string|max:30',
            'position' => 'nullable|string|max:120',
            'bio' => 'nullable|string|max:1000',
            'instagram' => 'nullable|url|max:200',
            'facebook' => 'nullable|url|max:200',
            'password' => 'nullable|string|min:6',
            'photo_file' => 'nullable|image|max:2048',
        ]);

        unset($data['photo_file']);
        if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
        else unset($data['password']);

        if ($this->photo_file) {
            $data['photo'] = $this->photo_file->store('photos', 'public');
        }

        $u->update($data);
        $this->password = '';
        $this->photo_file = null;
        $this->notifySuccess('Profil berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.guru.profile')
            ->layout('layouts.panel', ['title' => 'Profil']);
    }
}
