<?php

use App\Jobs\GenerateNewsArticle;
use App\Livewire\Admin\News as AdminNews;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'ai.providers.gemini.api_key' => 'test-key',
        'ai.providers.gemini.base_url' => 'https://ai.example.test/v1',
    ]);
});

test('generateWithAi dispatches a queued job instead of blocking', function () {
    Queue::fake();
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $component = Livewire::actingAs($admin)
        ->test(AdminNews::class)
        ->set('aiPrompt', 'Buatkan berita lomba sains')
        ->call('generateWithAi');

    Queue::assertPushed(GenerateNewsArticle::class);
    $component->assertSet('aiLoading', true);
    expect($component->get('aiJobKey'))->not->toBeNull();
});

test('pollAi applies the job result to the form', function () {
    Http::fake([
        '*' => Http::response([
            'choices' => [
                ['message' => ['content' => json_encode([
                    'title' => 'Lomba Sains Sukses',
                    'category' => 'KEGIATAN',
                    'excerpt' => 'Ringkasan',
                    'content' => '<p>Isi berita</p>',
                ])]],
            ],
        ]),
    ]);

    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    // Sync queue runs the job during dispatch, filling the cache.
    $component = Livewire::actingAs($admin)
        ->test(AdminNews::class)
        ->set('aiPrompt', 'Buatkan berita lomba sains')
        ->call('generateWithAi')
        ->call('pollAi');

    $component
        ->assertSet('aiLoading', false)
        ->assertSet('category', 'KEGIATAN')
        ->assertSet('aiJobKey', null);
});
