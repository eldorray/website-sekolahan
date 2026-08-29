<?php

use App\Models\News;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('images:webp re-encodes old uploads and rewrites every stored reference', function () {
    Storage::fake('public');
    $disk = Storage::disk('public');
    $disk->put('news/sample.jpg', UploadedFile::fake()->image('sample.jpg', 2000, 1500)->get());

    $user = User::factory()->create();
    $news = News::create([
        'user_id' => $user->id, 'title' => 'Berita', 'slug' => 'berita',
        'category' => 'ARTIKEL', 'excerpt' => 'e',
        // Rich-text bodies embed the path inline, not just in the image column.
        'content' => '<p><img src="/storage/news/sample.jpg"></p>',
        'image' => 'news/sample.jpg', 'is_published' => true,
    ]);
    Setting::set('hero_image', 'news/sample.jpg');

    $this->artisan('images:webp')->assertSuccessful();

    expect($disk->exists('news/sample.webp'))->toBeTrue()
        ->and($disk->exists('news/sample.jpg'))->toBeFalse();

    $news->refresh();
    expect($news->image)->toBe('news/sample.webp')
        ->and($news->content)->toContain('/storage/news/sample.webp')
        ->and($news->content)->not->toContain('sample.jpg')
        ->and(Setting::get('hero_image'))->toBe('news/sample.webp');
});

test('images:webp dry run changes nothing', function () {
    Storage::fake('public');
    Storage::disk('public')->put('news/keep.jpg', UploadedFile::fake()->image('keep.jpg', 400, 300)->get());

    $this->artisan('images:webp', ['--dry-run' => true])->assertSuccessful();

    expect(Storage::disk('public')->exists('news/keep.jpg'))->toBeTrue()
        ->and(Storage::disk('public')->exists('news/keep.webp'))->toBeFalse();
});
