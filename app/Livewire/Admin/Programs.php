<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\Program;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Programs extends Component
{
    use WithPagination, WithFileUploads, WithNotifications, WithDeleteConfirm;

    public ?int $editingId = null;
    public string $title = '';
    public string $icon = 'book';
    public string $short_description = '';
    public string $description = '';
    public bool $is_active = true;
    public int $order = 0;
    public $image_file;
    public ?string $existing_image = null;

    public string $search = '';

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:160',
            'icon' => 'nullable|string|max:60',
            'short_description' => 'required|string|max:300',
            'description' => 'nullable|string',
            'order' => 'integer',
            'is_active' => 'boolean',
            'image_file' => 'nullable|image|max:2048',
        ];
    }

    public function edit(int $id): void
    {
        $p = Program::findOrFail($id);
        $this->editingId = $p->id;
        $this->title = $p->title;
        $this->icon = $p->icon;
        $this->short_description = $p->short_description;
        $this->description = $p->description ?? '';
        $this->order = $p->order;
        $this->is_active = $p->is_active;
        $this->existing_image = $p->image;
        $this->image_file = null;
    }

    public function save(): void
    {
        $data = $this->validate();
        unset($data['image_file']);
        $data['slug'] = Str::slug($this->title);

        if ($this->image_file) {
            $data['image'] = $this->image_file->store('programs', 'public');
        }

        if ($this->editingId) {
            Program::find($this->editingId)->update($data);
            $this->notifySuccess('Program berhasil diperbarui.');
        } else {
            Program::create($data);
            $this->notifySuccess('Program berhasil dibuat.');
        }
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Program::findOrFail($id)->delete();
        $this->notifySuccess('Program berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'icon', 'short_description', 'description', 'order', 'is_active', 'image_file', 'existing_image']);
        $this->is_active = true;
        $this->icon = 'book';
    }

    public function render()
    {
        $items = Program::when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy('order')->paginate(10);

        return view('livewire.admin.programs', compact('items'))
            ->layout('layouts.panel', ['title' => 'Program']);
    }
}
