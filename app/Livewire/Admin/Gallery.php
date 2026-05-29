<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\GalleryAlbum;
use App\Services\ImageProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Gallery extends Component
{
    use WithDeleteConfirm, WithFileUploads, WithNotifications, WithPagination;

    public ?int $editingId = null;

    public string $title = '';

    public string $description = '';

    public int $order = 0;

    public bool $is_published = true;

    public $cover_file = null;

    public ?string $existing_cover = null;

    public string $search = '';

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:160',
            'description' => 'nullable|string|max:1000',
            'order' => 'integer',
            'is_published' => 'boolean',
            'cover_file' => 'nullable|image|max:6144',
        ];
    }

    public function edit(int $id): void
    {
        $a = GalleryAlbum::findOrFail($id);
        $this->editingId = $a->id;
        $this->title = $a->title;
        $this->description = $a->description ?? '';
        $this->order = $a->order;
        $this->is_published = $a->is_published;
        $this->existing_cover = $a->cover_image;
        $this->cover_file = null;
    }

    public function save(): void
    {
        $data = $this->validate();
        unset($data['cover_file']);

        if ($this->cover_file) {
            $resized = ImageProcessor::storeResized(
                $this->cover_file,
                'gallery/covers',
                maxWidth: 1200,
                thumbWidth: 480,
            );
            $data['cover_image'] = $resized['image'];

            if ($this->editingId && $this->existing_cover) {
                Storage::disk('public')->delete($this->existing_cover);
            }
        }

        if ($this->editingId) {
            $album = GalleryAlbum::find($this->editingId);
            $album->update($data);
            $this->notifySuccess('Album berhasil diperbarui.');
        } else {
            $data['slug'] = Str::slug($this->title).'-'.Str::random(4);
            GalleryAlbum::create($data);
            $this->notifySuccess('Album berhasil dibuat.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $a = GalleryAlbum::findOrFail($id);
        // Delete all photos files first.
        foreach ($a->photos as $p) {
            ImageProcessor::delete($p->image, $p->thumbnail);
        }
        if ($a->cover_image) {
            Storage::disk('public')->delete($a->cover_image);
        }
        $a->delete();
        $this->notifySuccess('Album dan seluruh fotonya berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingId', 'title', 'description', 'order', 'cover_file', 'existing_cover',
        ]);
        $this->is_published = true;
    }

    public function render()
    {
        $items = GalleryAlbum::query()
            ->withCount('photos')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy('order')
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.gallery', compact('items'))
            ->layout('layouts.panel', ['title' => 'Galeri']);
    }
}
