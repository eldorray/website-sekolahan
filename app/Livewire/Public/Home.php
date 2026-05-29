<?php

namespace App\Livewire\Public;

use App\Models\Brochure;
use App\Models\GalleryAlbum;
use App\Models\News;
use App\Models\Program;
use App\Models\Setting;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class Home extends Component
{
    public function render()
    {
        $programs = Program::where('is_active', true)->orderBy('order')->take(6)->get();
        $news = News::where('is_published', true)->orderByDesc('published_at')->take(4)->get();
        $teachers = User::where('role', 'guru')->where('is_active', true)->take(8)->get();
        $brochures = Brochure::where('is_active', true)
            ->with('images')
            ->orderBy('order')
            ->orderByDesc('id')
            ->take(4)
            ->get();
        $albums = GalleryAlbum::where('is_published', true)
            ->withCount('photos')
            ->orderBy('order')
            ->orderByDesc('id')
            ->take(4)
            ->get();
        $school = Setting::get('school_name', 'Alifia Modern School');
        $categories = $news->pluck('category')->unique()->prepend('Semua')->values();

        return view('livewire.public.home', compact('programs', 'news', 'teachers', 'brochures', 'albums', 'school', 'categories'));
    }
}
