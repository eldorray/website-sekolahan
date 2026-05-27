<?php

namespace App\Livewire\Public;

use App\Models\News;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.public')]
class NewsIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $items = News::where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('livewire.public.news-index', compact('items'));
    }
}
