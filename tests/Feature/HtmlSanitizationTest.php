<?php

use App\Livewire\Admin\News as AdminNews;
use App\Livewire\Admin\Programs;
use App\Models\News;
use App\Models\Program;
use App\Models\User;
use App\Support\HtmlSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('sanitizer strips script tags and event handlers', function () {
    $dirty = '<p>Halo</p><script>alert(1)</script><img src=x onerror="alert(2)">';

    $clean = HtmlSanitizer::clean($dirty);

    expect($clean)
        ->not->toContain('<script')
        ->not->toContain('onerror')
        ->and($clean)->toContain('<p>Halo</p>');
});

test('sanitizer drops javascript: protocol on links', function () {
    $clean = HtmlSanitizer::clean('<a href="javascript:alert(1)">klik</a>');

    expect($clean)->not->toContain('javascript:');
});

test('sanitizer keeps whitelisted formatting tags', function () {
    $html = '<h2>Judul</h2><p><strong>tebal</strong> <em>miring</em></p><ul><li>satu</li></ul><blockquote>kutipan</blockquote>';

    $clean = HtmlSanitizer::clean($html);

    expect($clean)
        ->toContain('<h2>')
        ->toContain('<strong>')
        ->toContain('<em>')
        ->toContain('<li>')
        ->toContain('<blockquote>');
});

test('admin news save stores sanitized content', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(AdminNews::class)
        ->set('title', 'Berita Uji')
        ->set('category', 'ARTIKEL')
        ->set('excerpt', 'ringkasan singkat')
        ->set('content', '<p>aman</p><script>alert("xss")</script>')
        ->set('is_published', true)
        ->call('save')
        ->assertHasNoErrors();

    $news = News::firstWhere('title', 'Berita Uji');

    expect($news)->not->toBeNull()
        ->and($news->content)->not->toContain('<script')
        ->and($news->content)->toContain('<p>aman</p>');
});

test('program save stores sanitized description', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test(Programs::class)
        ->set('title', 'Program Uji')
        ->set('icon', 'book')
        ->set('short_description', 'deskripsi singkat')
        ->set('description', '<p>oke</p><img src=x onerror=alert(1)>')
        ->call('save')
        ->assertHasNoErrors();

    $program = Program::firstWhere('title', 'Program Uji');

    expect($program)->not->toBeNull()
        ->and($program->description)->not->toContain('onerror');
});
