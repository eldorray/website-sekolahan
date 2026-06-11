<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\News as NewsModel;
use App\Services\AiWriter;
use App\Services\ImageProcessor;
use App\Support\HtmlSanitizer;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class News extends Component
{
    use WithDeleteConfirm, WithFileUploads, WithNotifications, WithPagination;

    public ?int $editingId = null;

    public string $title = '';

    public string $category = 'ARTIKEL';

    public string $excerpt = '';

    public string $content = '';

    public string $published_at = '';

    public bool $is_published = true;

    public $image_file;

    public ?string $existing_image = null;

    public string $search = '';

    // AI Chat
    public string $aiPrompt = '';

    public string $aiProvider = '';

    public bool $aiLoading = false;

    public string $aiError = '';

    public array $aiMessages = [];

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
        $n = NewsModel::findOrFail($id);
        $this->editingId = $n->id;
        $this->title = $n->title;
        $this->category = $n->category;
        $this->excerpt = $n->excerpt;
        $this->content = $n->content;
        $this->published_at = $n->published_at?->format('Y-m-d') ?? '';
        $this->is_published = $n->is_published;
        $this->image_file = null;
        $this->existing_image = $n->image;
    }

    public function save(): void
    {
        $data = $this->validate();
        unset($data['image_file']);
        $data['content'] = HtmlSanitizer::clean($data['content']);
        $data['slug'] = Str::slug($this->title).'-'.Str::random(4);
        $data['user_id'] = auth()->id();
        $data['published_at'] = $this->published_at ?: now()->toDateString();

        if ($this->image_file) {
            // Compress & resize the uploaded image before storing.
            $newImage = ImageProcessor::storeCompressed(
                $this->image_file,
                'news',
                maxWidth: 1280,
                quality: 72,
            );

            // Remove previous image when replacing during edit.
            if ($this->editingId && $this->existing_image && $this->existing_image !== $newImage) {
                ImageProcessor::delete($this->existing_image);
            }

            $data['image'] = $newImage;
        } elseif ($this->editingId && $this->existing_image === null) {
            // Image was explicitly removed during edit
            $data['image'] = null;
        }

        if ($this->editingId) {
            unset($data['slug']);
            NewsModel::find($this->editingId)->update($data);
            $this->notifySuccess('Berita berhasil diperbarui.');
        } else {
            NewsModel::create($data);
            $this->notifySuccess('Berita berhasil dibuat.');
        }
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $news = NewsModel::findOrFail($id);
        if ($news->image) {
            ImageProcessor::delete($news->image);
        }
        $news->delete();
        $this->notifySuccess('Berita berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'excerpt', 'content', 'published_at', 'image_file', 'existing_image']);
        $this->category = 'ARTIKEL';
        $this->is_published = true;
    }

    public function removeImage(): void
    {
        $this->image_file = null;
        $this->existing_image = null;
    }

    public function generateWithAi(): void
    {
        if (empty(trim($this->aiPrompt))) {
            $this->aiError = 'Tulis instruksi terlebih dahulu.';

            return;
        }

        $this->aiLoading = true;
        $this->aiError = '';

        // Add user message to chat
        $this->aiMessages[] = ['role' => 'user', 'text' => $this->aiPrompt];

        try {
            $provider = $this->aiProvider ?: config('ai.default', 'gemini');
            $result = AiWriter::generateNews($this->aiPrompt, $provider);

            // Set category and date directly (no typewriter for these)
            $this->category = $result['category'];
            $this->published_at = now()->toDateString();

            // Add AI response to chat
            $this->aiMessages[] = [
                'role' => 'ai',
                'text' => "Berita berhasil dibuat! Judul: \"{$result['title']}\". Lihat efek pengetikan di form.",
            ];

            // Trigger typewriter effect via window CustomEvent (more reliable than Livewire event)
            $payload = json_encode([
                'title' => $result['title'],
                'excerpt' => $result['excerpt'],
                'content' => $result['content'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $this->js("setTimeout(() => window.dispatchEvent(new CustomEvent('ai-typewriter', { detail: {$payload} })), 100)");

            $this->notifySuccess('Berita berhasil di-generate oleh AI!');
        } catch (\Throwable $e) {
            $this->aiError = 'Gagal generate: '.$e->getMessage();
            $this->aiMessages[] = ['role' => 'ai', 'text' => '❌ '.$this->aiError];
        } finally {
            $this->aiLoading = false;
            $this->aiPrompt = '';
        }
    }

    public function clearAiChat(): void
    {
        $this->aiMessages = [];
        $this->aiError = '';
        $this->aiPrompt = '';
    }

    public function render()
    {
        $items = NewsModel::with('author')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->latest()->paginate(10);

        return view('livewire.admin.news', compact('items'))
            ->layout('layouts.panel', ['title' => 'Berita']);
    }
}
