<?php

namespace App\Livewire\Guru;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\News as NewsModel;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MyNews extends Component
{
    use WithPagination, WithFileUploads, WithNotifications, WithDeleteConfirm;

    public ?int $editingId = null;
    public string $title = '';
    public string $category = 'ARTIKEL';
    public string $excerpt = '';
    public string $content = '';
    public string $published_at = '';
    public bool $is_published = false;
    public $image_file;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'category' => 'required|string|max:60',
            'excerpt' => 'required|string|max:400',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
            'image_file' => 'nullable|image|max:4096',
        ];
    }

    public function edit(int $id): void
    {
        $n = NewsModel::where('user_id', auth()->id())->findOrFail($id);
        $this->editingId = $n->id;
        $this->title = $n->title;
        $this->category = $n->category;
        $this->excerpt = $n->excerpt;
        $this->content = $n->content;
        $this->published_at = $n->published_at?->format('Y-m-d') ?? '';
        $this->is_published = $n->is_published;
    }

    public function save(): void
    {
        $data = $this->validate();
        unset($data['image_file']);
        $data['user_id'] = auth()->id();
        $data['published_at'] = $this->published_at ?: now()->toDateString();

        if ($this->image_file) {
            $data['image'] = $this->image_file->store('news', 'public');
        }

        if ($this->editingId) {
            NewsModel::where('user_id', auth()->id())->where('id', $this->editingId)->update($data);
            $this->notifySuccess('Berita berhasil diperbarui.');
        } else {
            $data['slug'] = Str::slug($this->title) . '-' . Str::random(4);
            NewsModel::create($data);
            $this->notifySuccess('Berita berhasil dibuat.');
        }
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        NewsModel::where('user_id', auth()->id())->findOrFail($id)->delete();
        $this->notifySuccess('Berita berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'excerpt', 'content', 'published_at', 'image_file']);
        $this->category = 'ARTIKEL';
        $this->is_published = false;
    }

    public function render()
    {
        $items = NewsModel::where('user_id', auth()->id())->latest()->paginate(10);
        return view('livewire.guru.my-news', compact('items'))
            ->layout('layouts.panel', ['title' => 'Berita Saya']);
    }
}
