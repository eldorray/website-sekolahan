<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\Brochure;
use App\Models\BrochureImage;
use App\Services\ImageProcessor;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Brochures extends Component
{
    use WithDeleteConfirm;
    use WithFileUploads;
    use WithNotifications;
    use WithPagination;

    public ?int $editingId = null;

    public string $title = '';

    public string $subtitle = '';

    public int $order = 0;

    public bool $is_active = true;

    /** @var array<int, TemporaryUploadedFile> */
    public array $images = [];

    public $file_pdf = null;

    public ?string $existing_file = null;

    public string $search = '';

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:160',
            'subtitle' => 'nullable|string|max:200',
            'order' => 'integer',
            'is_active' => 'boolean',
            'images.*' => 'image|max:6144',
            'file_pdf' => 'nullable|file|mimes:pdf|max:10240',
        ];
    }

    public function edit(int $id): void
    {
        $b = Brochure::findOrFail($id);
        $this->editingId = $b->id;
        $this->title = $b->title;
        $this->subtitle = $b->subtitle ?? '';
        $this->order = $b->order;
        $this->is_active = $b->is_active;
        $this->existing_file = $b->file;
        $this->images = [];
        $this->file_pdf = null;
    }

    public function save(): void
    {
        $this->validate();

        $payload = [
            'title' => $this->title,
            'subtitle' => $this->subtitle ?: null,
            'order' => $this->order,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            $brochure = Brochure::findOrFail($this->editingId);
            $brochure->update($payload);
            $this->notifySuccess('Brosur berhasil diperbarui.');
        } else {
            if (empty($this->images)) {
                $this->addError('images', 'Minimal satu gambar preview wajib diunggah.');

                return;
            }
            $brochure = Brochure::create($payload);
            $this->notifySuccess('Brosur berhasil dibuat.');
        }

        // Append uploaded images.
        if (! empty($this->images)) {
            $existingCount = $brochure->images()->count();
            foreach ($this->images as $i => $file) {
                $resized = ImageProcessor::storeResized(
                    $file,
                    'brochures/'.$brochure->id,
                    maxWidth: 1200,
                    thumbWidth: 480,
                );

                BrochureImage::create([
                    'brochure_id' => $brochure->id,
                    'image' => $resized['image'],
                    'thumbnail' => $resized['thumbnail'],
                    'order' => $existingCount + $i,
                    'is_cover' => $existingCount === 0 && $i === 0,
                ]);
            }
        }

        if ($this->file_pdf) {
            if ($this->editingId && $this->existing_file) {
                Storage::disk('public')->delete($this->existing_file);
            }
            $brochure->update(['file' => $this->file_pdf->store('brochures/files', 'public')]);
        }

        $this->resetForm();
    }

    public function deleteImage(int $imageId): void
    {
        $image = BrochureImage::findOrFail($imageId);
        if ($this->editingId !== $image->brochure_id) {
            return;
        }
        ImageProcessor::delete($image->image, $image->thumbnail);
        $wasCover = $image->is_cover;
        $image->delete();

        // Promote another image to cover if needed.
        if ($wasCover) {
            $next = BrochureImage::where('brochure_id', $this->editingId)->orderBy('order')->first();
            $next?->update(['is_cover' => true]);
        }
        $this->notifySuccess('Gambar dihapus.');
    }

    public function setCover(int $imageId): void
    {
        $image = BrochureImage::findOrFail($imageId);
        if ($this->editingId !== $image->brochure_id) {
            return;
        }
        BrochureImage::where('brochure_id', $this->editingId)->update(['is_cover' => false]);
        $image->update(['is_cover' => true]);
        $this->notifySuccess('Cover diperbarui.');
    }

    public function delete(int $id): void
    {
        $b = Brochure::findOrFail($id);
        foreach ($b->images as $img) {
            ImageProcessor::delete($img->image, $img->thumbnail);
        }
        if ($b->file) {
            Storage::disk('public')->delete($b->file);
        }
        if ($b->preview_image) {
            Storage::disk('public')->delete($b->preview_image);
        }
        $b->delete();
        $this->notifySuccess('Brosur berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingId', 'title', 'subtitle', 'order',
            'images', 'file_pdf', 'existing_file',
        ]);
        $this->is_active = true;
    }

    public function render()
    {
        $items = Brochure::query()
            ->with('images')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy('order')
            ->orderByDesc('id')
            ->paginate(10);

        $editingImages = $this->editingId
            ? BrochureImage::where('brochure_id', $this->editingId)->orderByDesc('is_cover')->orderBy('order')->get()
            : collect();

        return view('livewire.admin.brochures', compact('items', 'editingImages'))
            ->layout('layouts.panel', ['title' => 'Brosur']);
    }
}
