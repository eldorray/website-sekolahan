<?php

use App\Models\AuditLog;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('deleting news soft-deletes and can be restored', function () {
    $user = User::factory()->create();
    $news = News::create([
        'user_id' => $user->id,
        'title' => 'Berita',
        'slug' => 'berita-x',
        'category' => 'ARTIKEL',
        'excerpt' => 'e',
        'content' => '<p>c</p>',
    ]);

    $news->delete();

    expect(News::find($news->id))->toBeNull()                 // hidden by default
        ->and(News::withTrashed()->find($news->id))->not->toBeNull();

    News::withTrashed()->find($news->id)->restore();

    expect(News::find($news->id))->not->toBeNull();
});

test('audit log records create, update, delete with the acting user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $news = News::create([
        'user_id' => $user->id,
        'title' => 'Asli',
        'slug' => 'asli',
        'category' => 'ARTIKEL',
        'excerpt' => 'e',
        'content' => '<p>c</p>',
    ]);

    $news->update(['title' => 'Diubah']);
    $news->delete();

    $events = AuditLog::where('auditable_type', News::class)
        ->where('auditable_id', $news->id)
        ->orderBy('id')
        ->pluck('event')
        ->all();

    expect($events)->toBe(['created', 'updated', 'deleted']);

    $updateLog = AuditLog::where('event', 'updated')->first();
    expect($updateLog->user_id)->toBe($user->id)
        ->and($updateLog->changes)->toHaveKey('title')
        ->and($updateLog->changes['title'])->toBe('Diubah');
});
