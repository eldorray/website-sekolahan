<?php

namespace App\Livewire\Public;

use App\Models\News;
use App\Support\Seo;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class NewsShow extends Component
{
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $news = News::with('author')
            ->where('slug', $this->slug)
            ->where('is_published', true)
            ->firstOrFail();

        $related = News::where('is_published', true)
            ->where('id', '!=', $news->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        app(Seo::class)->set(
            title: $news->title,
            description: $news->excerpt,
            image: $news->imageUrl(),
            type: 'article',
        );

        return view('livewire.public.news-show', compact('news', 'related'))
            ->title($news->title);
    }
}
