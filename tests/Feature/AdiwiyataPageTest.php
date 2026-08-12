<?php

use App\Models\AdiwiyataAssessment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['features.adiwiyata' => true, 'features.adiwiyata_pin' => '246810']);
    RateLimiter::clear('adiwiyata-unlock|127.0.0.1');
});

test('page renders and is linked from the public nav when enabled', function () {
    $this->get(route('adiwiyata'))
        ->assertOk()
        ->assertSee('Monitoring Kelengkapan Dokumen Adiwiyata')
        // @json() escapes slashes, so compare against the encoded form.
        ->assertSee(json_encode(route('adiwiyata.data')), false);

    $this->get(route('adiwiyata.data'))->assertOk();
    $this->get(route('home'))->assertSee(route('adiwiyata'), false);
    $this->get(route('sitemap'))->assertSee(route('adiwiyata'), false);
});

test('everything adiwiyata is unreachable when the feature is off', function () {
    config(['features.adiwiyata' => false]);

    $this->get(route('adiwiyata'))->assertNotFound();
    $this->get(route('adiwiyata.data'))->assertNotFound();
    $this->post(route('adiwiyata.unlock'), ['pin' => '246810'])->assertNotFound();
    $this->get(route('home'))->assertDontSee(route('adiwiyata'), false);
    $this->get(route('sitemap'))->assertDontSee(route('adiwiyata'), false);
});

test('saving is refused without the PIN', function () {
    $this->postJson(route('adiwiyata.save'), [
        'folder_key' => 'A / B',
        'status' => 'ok',
    ])->assertForbidden();

    $this->postJson(route('adiwiyata.reset'))->assertForbidden();

    expect(AdiwiyataAssessment::count())->toBe(0);
});

test('a wrong PIN does not unlock', function () {
    $this->post(route('adiwiyata.unlock'), ['pin' => '000000'])
        ->assertSessionHasErrors('pin');

    $this->postJson(route('adiwiyata.save'), [
        'folder_key' => 'A / B',
        'status' => 'ok',
    ])->assertForbidden();
});

test('the correct PIN unlocks saving, and the result is shared across browsers', function () {
    $this->post(route('adiwiyata.unlock'), ['pin' => '246810'])->assertRedirect();

    $this->postJson(route('adiwiyata.save'), [
        'folder_key' => '2026 / 3. Data Penggunaan air',
        'status' => 'ok',
        'note' => 'sudah diunggah',
    ])->assertOk()->assertJsonPath('status', 'ok');

    // Sesi baru = browser/perangkat lain. Harus melihat penilaian yang sama.
    $this->flushSession();

    $this->get(route('adiwiyata'))
        ->assertOk()
        ->assertSee('2026 \/ 3. Data Penggunaan air', false)
        ->assertSee('sudah diunggah', false);
});

test('brute forcing the PIN is rate limited', function () {
    foreach (range(1, 5) as $i) {
        $this->post(route('adiwiyata.unlock'), ['pin' => 'salah'.$i])
            ->assertSessionHasErrors('pin');
    }

    $this->post(route('adiwiyata.unlock'), ['pin' => '246810'])
        ->assertSessionHasErrors('pin');

    $this->postJson(route('adiwiyata.save'), [
        'folder_key' => 'A / B',
        'status' => 'ok',
    ])->assertForbidden();
});

test('saving is impossible when no PIN is configured', function () {
    config(['features.adiwiyata_pin' => '']);

    $this->post(route('adiwiyata.unlock'), ['pin' => ''])->assertNotFound();
    $this->get(route('adiwiyata'))->assertOk()->assertDontSee('name="pin"', false);
});

test('save input is validated', function () {
    $this->post(route('adiwiyata.unlock'), ['pin' => '246810']);

    $this->postJson(route('adiwiyata.save'), ['folder_key' => 'A', 'status' => 'ngawur'])
        ->assertJsonValidationErrors('status');

    $this->postJson(route('adiwiyata.save'), ['folder_key' => str_repeat('x', 256), 'status' => 'ok'])
        ->assertJsonValidationErrors('folder_key');
});

test('scan snapshot is valid json with a drive-linked root', function () {
    $tree = json_decode(file_get_contents(resource_path('data/adiwiyata-tree.json')), true);

    expect($tree['root']['id'])->not->toBeEmpty()
        ->and($tree['root']['c'])->not->toBeEmpty();
});
