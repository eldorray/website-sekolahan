<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\Brochure;
use App\Services\ImageProcessor;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Brochures extends Component
{
    use WithDeleteConfirm, WithFileUploads, WithNotifications, WithPagination;

    public ?int $editingId = null;

    public string $title = '';

    public string $subtitle = '';

    public int $order = 0;

    public bool $is_active = true;

    public $preview_file = null;

    public $file_pdf = null;

    public ?string $existing_preview = null;

    public ?string $existing_file = null;

    public string $search = '';

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:160',
            'subtitle' => 'nullable|string|max:200',
            'order' => 'integer',
            'is_active' => 'boolean',
            'preview_file' => 'nullable|image|max:6144',
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
        $this->existing_preview = $b->preview_image;
        $this->existing_file = $b->file;
        $this->preview_file = null;
        $this->file_pdf = null;
    }

    public function save(): void
    {
        $data = $this->validate();
        unset($data['preview_file'], $data['file_pdf']);

        if ($this->preview_file) {
            $resized = ImageProcessor::storeResized(
                $this->preview_file,
                'brochures',
                maxWidth: 1200,
                thumbWidth: 480,
            );
            $data['preview_image'] = $resized['image'];

            if ($this->editingId && $this->existing_preview) {
                Storage::disk('public')->delete($this->existing_preview);
            }
        } elseif (! $this->editingId) {
            $this->addError('preview_file', 'Gambar preview brosur wajib diisi.');

            return;
        }

        if ($this->file_pdf) {
            $data['file'] = $this->file_pdf->store('brochures/files', 'public');
            if ($this->editingId && $this->existing_file) {
                Storage::disk('public')->delete($this->existing_file);
            }
        }

        if ($this->editingId) {
            Brochure::find($this->editingId)->update($data);
            $this->notifySuccess('Brosur berhasil diperbarui.');
        } else {
            Brochure::create($data);
            $this->notifySuccess('Brosur berhasil dibuat.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $b = Brochure::findOrFail($id);
        if ($b->preview_image) {
            Storage::disk('public')->delete($b->preview_image);
        }
        if ($b->file) {
            Storage::disk('public')->delete($b->file);
        }
        $b->delete();
        $this->notifySuccess('Brosur berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingId', 'title', 'subtitle', 'order', 'preview_file', 'file_pdf',
            'existing_preview', 'existing_file',
        ]);
        $this->is_active = true;
    }

    public function render()
    {
        $items = Brochure::query()
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy('order')
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.brochures', compact('items'))
            ->layout('layouts.panel', ['title' => 'Brosur']);
    }
}
