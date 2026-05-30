<?php

namespace App\Livewire\Public;

use App\Models\Brochure;
use App\Models\GalleryAlbum;
use App\Models\News;
use App\Models\Program;
use Illuminate\Support\Str;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $q = '';

    /**
     * Static destinations always searchable (pages).
     *
     * @var array<int, array{label: string, route: string, type: string}>
     */
    protected array $pages = [
        ['label' => 'Beranda', 'route' => 'home', 'type' => 'Halaman'],
        ['label' => 'Tentang Kami', 'route' => 'about', 'type' => 'Halaman'],
        ['label' => 'Program', 'route' => 'programs.index', 'type' => 'Halaman'],
        ['label' => 'Berita', 'route' => 'news.index', 'type' => 'Halaman'],
        ['label' => 'Tim Guru', 'route' => 'teachers.index', 'type' => 'Halaman'],
        ['label' => 'Kontak', 'route' => 'contact', 'type' => 'Halaman'],
        ['label' => 'PPDB / Pendaftaran', 'route' => 'ppdb.create', 'type' => 'Halaman'],
    ];

    /**
     * Home-page sections (anchors) that have no dedicated route.
     *
     * @var array<int, array{label: string, anchor: string, icon: string}>
     */
    protected array $sections = [
        ['label' => 'Brosur', 'anchor' => 'brochures', 'icon' => 'document-text'],
        ['label' => 'Galeri Kegiatan', 'anchor' => 'gallery', 'icon' => 'photo'],
    ];

    public function getResultsProperty(): array
    {
        $term = trim($this->q);
        if (Str::length($term) < 2) {
            return [];
        }

        $results = [];

        // Pages
        foreach ($this->pages as $page) {
            if (Str::contains(Str::lower($page['label']), Str::lower($term))) {
                $results[] = [
                    'type' => $page['type'],
                    'title' => $page['label'],
                    'subtitle' => null,
                    'url' => route($page['route']),
                    'icon' => 'document',
                ];
            }
        }

        // Home sections (anchors)
        foreach ($this->sections as $section) {
            if (Str::contains(Str::lower($section['label']), Str::lower($term))) {
                $results[] = [
                    'type' => 'Halaman',
                    'title' => $section['label'],
                    'subtitle' => null,
                    'url' => route('home').'#'.$section['anchor'],
                    'icon' => $section['icon'],
                ];
            }
        }

        // News
        News::where('is_published', true)
            ->where(function ($query) use ($term) {
                $query->where('title', 'like', "%{$term}%")
                    ->orWhere('excerpt', 'like', "%{$term}%")
                    ->orWhere('category', 'like', "%{$term}%");
            })
            ->orderByDesc('published_at')
            ->limit(5)
            ->get()
            ->each(function (News $news) use (&$results) {
                $results[] = [
                    'type' => 'Berita',
                    'title' => $news->title,
                    'subtitle' => $news->category,
                    'url' => route('news.show', $news->slug),
                    'icon' => 'newspaper',
                ];
            });

        // Programs
        Program::where('is_active', true)
            ->where(function ($query) use ($term) {
                $query->where('title', 'like', "%{$term}%")
                    ->orWhere('short_description', 'like', "%{$term}%");
            })
            ->orderBy('order')
            ->limit(5)
            ->get()
            ->each(function (Program $program) use (&$results) {
                $results[] = [
                    'type' => 'Program',
                    'title' => $program->title,
                    'subtitle' => Str::limit($program->short_description, 60),
                    'url' => route('programs.show', $program->slug),
                    'icon' => 'academic-cap',
                ];
            });

        // Gallery albums
        GalleryAlbum::where('is_published', true)
            ->where('title', 'like', "%{$term}%")
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->each(function (GalleryAlbum $album) use (&$results) {
                $results[] = [
                    'type' => 'Galeri',
                    'title' => $album->title,
                    'subtitle' => $album->photos()->count().' foto',
                    'url' => route('gallery.album', $album->slug),
                    'icon' => 'photo',
                ];
            });

        // Brochures (no dedicated page — link to the home brochures section)
        Brochure::where('is_active', true)
            ->where(function ($query) use ($term) {
                $query->where('title', 'like', "%{$term}%")
                    ->orWhere('subtitle', 'like', "%{$term}%");
            })
            ->orderBy('order')
            ->limit(5)
            ->get()
            ->each(function (Brochure $brochure) use (&$results) {
                $results[] = [
                    'type' => 'Brosur',
                    'title' => $brochure->title,
                    'subtitle' => $brochure->subtitle ?: 'Brosur sekolah',
                    'url' => route('home').'#brochures',
                    'icon' => 'document-text',
                ];
            });

        return $results;
    }

    public function render()
    {
        return view('livewire.public.global-search', [
            'results' => $this->results,
        ]);
    }
}
