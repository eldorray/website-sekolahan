<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Teachers extends Component
{
    use WithDeleteConfirm, WithFileUploads, WithNotifications, WithPagination;

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $position = '';

    public string $phone = '';

    public string $bio = '';

    public string $instagram = '';

    public string $facebook = '';

    public bool $is_active = true;

    public $photo_file;

    public string $search = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:160',
            'email' => 'required|email|max:160|unique:users,email,'.($this->editingId ?? 'NULL'),
            'password' => $this->editingId ? 'nullable|string|min:6' : 'required|string|min:6',
            'position' => 'nullable|string|max:120',
            'phone' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:1000',
            'instagram' => 'nullable|url|max:200',
            'facebook' => 'nullable|url|max:200',
            'is_active' => 'boolean',
            'photo_file' => 'nullable|image|max:2048',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan.',
            'password.required' => 'Password wajib diisi untuk guru baru.',
            'password.min' => 'Password minimal 6 karakter.',
            'instagram.url' => 'Instagram harus berupa URL lengkap, mis. https://instagram.com/namaakun. Kosongkan jika tidak ada.',
            'facebook.url' => 'Facebook harus berupa URL lengkap, mis. https://facebook.com/namaakun. Kosongkan jika tidak ada.',
            'photo_file.image' => 'File foto harus berupa gambar.',
            'photo_file.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }

    public function updated(string $property): void
    {
        // Treat "-" or whitespace-only social fields as empty to avoid silent URL validation failures.
        if (in_array($property, ['instagram', 'facebook'], true)) {
            $value = trim((string) $this->{$property});
            if ($value === '-' || $value === '') {
                $this->{$property} = '';
            }
        }

        $this->validateOnly($property);
    }

    public function edit(int $id): void
    {
        $u = User::where('role', 'guru')->findOrFail($id);
        $this->editingId = $u->id;
        $this->name = $u->name;
        $this->email = $u->email;
        $this->password = '';
        $this->position = $u->position ?? '';
        $this->phone = $u->phone ?? '';
        $this->bio = $u->bio ?? '';
        $this->instagram = $u->instagram ?? '';
        $this->facebook = $u->facebook ?? '';
        $this->is_active = (bool) $u->is_active;
        $this->photo_file = null;
    }

    public function save(): void
    {
        try {
            $data = $this->validate();
        } catch (ValidationException $e) {
            $this->notifyError('Periksa kembali isian form. Beberapa data belum valid.');
            throw $e;
        }

        unset($data['photo_file']);
        $data['role'] = 'guru';

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($this->photo_file) {
            $data['photo'] = $this->photo_file->store('photos', 'public');
        }

        if ($this->editingId) {
            User::find($this->editingId)->update($data);
            $this->notifySuccess('Data guru berhasil diperbarui.');
        } else {
            User::create($data);
            $this->notifySuccess('Guru baru berhasil ditambahkan.');
        }
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        User::where('role', 'guru')->findOrFail($id)->delete();
        $this->notifySuccess('Guru berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'email', 'password', 'position', 'phone', 'bio', 'instagram', 'facebook', 'photo_file']);
        $this->is_active = true;
    }

    public function render()
    {
        $items = User::where('role', 'guru')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->paginate(10);

        return view('livewire.admin.teachers', compact('items'))
            ->layout('layouts.panel', ['title' => 'Guru']);
    }
}
