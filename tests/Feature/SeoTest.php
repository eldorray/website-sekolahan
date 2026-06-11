<?php

use App\Models\News;
use App\Models\Program;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('sitemap.xml lists static and published content urls', function () {
    Setting::set('school_name', 'Sekolah Uji');
    $user = User::factory()->create();

    $published = News::create([
        'user_id' => $user->id, 'title' => 'Berita Publik', 'slug' => 'berita-publik',
        'category' => 'ARTIKEL', 'excerpt' => 'e', 'content' => '<p>c</p>', 'is_published' => true,
    ]);
    News::create([
        'user_id' => $user->id, 'title' => 'Draft', 'slug' => 'draft-berita',
        'category' => 'ARTIKEL', 'excerpt' => 'e', 'content' => '<p>c</p>', 'is_published' => false,
    ]);
    Program::create([
        'title' => 'Program Aktif', 'slug' => 'program-aktif', 'icon' => 'book',
        'short_description' => 's', 'is_active' => true,
    ]);

    $response = $this->get('/sitemap.xml');

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee(route('news.show', 'berita-publik'), false)
        ->assertSee(route('programs.show', 'program-aktif'), false)
        ->assertSee(route('home'), false)
        ->assertDontSee(route('news.show', 'draft-berita'), false);
});

test('robots.txt references the sitemap and blocks panels', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Disallow: /admin')
        ->assertSee(route('sitemap'), false);
});

test('news show page emits article open graph tags', function () {
    Setting::set('school_name', 'Sekolah Uji');
    $user = User::factory()->create();
    News::create([
        'user_id' => $user->id, 'title' => 'Judul SEO', 'slug' => 'judul-seo',
        'category' => 'ARTIKEL', 'excerpt' => 'Ringkasan SEO', 'content' => '<p>c</p>', 'is_published' => true,
    ]);

    $response = $this->get('/berita/judul-seo');

    $response->assertOk()
        ->assertSee('<meta property="og:type" content="article">', false)
        ->assertSee('Judul SEO — Sekolah Uji', false)
        ->assertSee('Ringkasan SEO', false);
});
