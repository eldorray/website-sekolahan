<?php

namespace App\Livewire\Public;

use App\Models\GalleryAlbum;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class AlbumShow extends Component
{
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $album = GalleryAlbum::where('slug', $this->slug)
            ->where('is_published', true)
            ->firstOrFail();

        $photos = $album->photos()->paginate(24);

        return view('livewire.public.album-show', compact('album', 'photos'));
    }
}
