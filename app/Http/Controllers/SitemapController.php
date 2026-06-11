<?php

namespace App\Http\Controllers;

use App\Models\GalleryAlbum;
use App\Models\News;
use App\Models\Program;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [];

        // Static pages.
        foreach (['home', 'about', 'programs.index', 'news.index', 'teachers.index', 'contact', 'ppdb.create'] as $name) {
            $urls[] = ['loc' => route($name), 'lastmod' => null];
        }

        foreach (News::where('is_published', true)->get(['slug', 'updated_at']) as $n) {
            $urls[] = ['loc' => route('news.show', $n->slug), 'lastmod' => $n->updated_at?->toAtomString()];
        }

        foreach (Program::where('is_active', true)->get(['slug', 'updated_at']) as $p) {
            $urls[] = ['loc' => route('programs.show', $p->slug), 'lastmod' => $p->updated_at?->toAtomString()];
        }

        foreach (GalleryAlbum::where('is_published', true)->get(['slug', 'updated_at']) as $a) {
            $urls[] = ['loc' => route('gallery.album', $a->slug), 'lastmod' => $a->updated_at?->toAtomString()];
        }

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
