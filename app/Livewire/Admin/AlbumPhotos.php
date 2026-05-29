<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithDeleteConfirm;
use App\Livewire\Concerns\WithNotifications;
use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;
use App\Services\ImageProcessor;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class AlbumPhotos extends Component
{
    use WithDeleteConfirm, WithFileUploads, WithNotifications;

    public GalleryAlbum $album;

    /** @var array<int, TemporaryUploadedFile> */
    public array $uploads = [];

    public string $captionDraft = '';

    protected function rules(): array
    {
        return [
            'uploads.*' => 'image|max:8192',
        ];
    }

    public function mount(int $album): void
    {
        $this->album = GalleryAlbum::findOrFail($album);
    }

    public function uploadAll(): void
    {
        $this->validate();

        if (empty($this->uploads)) {
            $this->notifyError('Pilih minimal satu gambar untuk diunggah.');

            return;
        }

        $count = 0;
        foreach ($this->uploads as $file) {
            try {
                $resized = ImageProcessor::storeResized(
                    $file,
                    'gallery/photos/'.$this->album->id,
                    maxWidth: 1600,
                    thumbWidth: 480,
                    quality: 75,
                );

                GalleryPhoto::create([
                    'album_id' => $this->album->id,
                    'image' => $resized['image'],
                    'thumbnail' => $resized['thumbnail'],
                    'caption' => $this->captionDraft ?: null,
                    'order' => 0,
                ]);
                $count++;
            } catch (\Throwable $e) {
                report($e);
                $this->notifyError('Gagal memproses '.$file->getClientOriginalName());
            }
        }

        $this->reset(['uploads', 'captionDraft']);
        $this->notifySuccess($count.' foto berhasil diunggah.');
    }

    public function delete(int $id): void
    {
        $photo = GalleryPhoto::where('album_id', $this->album->id)->findOrFail($id);
        ImageProcessor::delete($photo->image, $photo->thumbnail);
        $photo->delete();
        $this->notifySuccess('Foto berhasil dihapus.');
    }

    public function render()
    {
        $photos = $this->album->photos()->paginate(24);

        return view('livewire.admin.album-photos', compact('photos'))
            ->layout('layouts.panel', ['title' => 'Foto Album: '.$this->album->title]);
    }
}
