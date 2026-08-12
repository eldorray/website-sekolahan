<?php

use App\Models\AdiwiyataAssessment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['features.adiwiyata' => true, 'features.adiwiyata_pin' => '246810']);
    RateLimiter::clear('adiwiyata-unlock|127.0.0.1');
});

function unlockAdiwiyata($test)
{
    return $test->post(route('adiwiyata.unlock'), ['pin' => '246810']);
}

test('visiting without a PIN shows only the lock screen', function () {
    AdiwiyataAssessment::create(['folder_key' => 'A / B', 'status' => 'ok', 'note' => 'rahasia']);

    $this->get(route('adiwiyata'))
        ->assertOk()
        ->assertSee('Masukkan PIN dari admin')
        ->assertSee('noindex', false)
        // Tidak ada isi halaman yang bocor: pohon, catatan, maupun URL datanya.
        ->assertDontSee('Progres Kelengkapan Folder')
        ->assertDontSee('rahasia')
        ->assertDontSee(json_encode(route('adiwiyata.data')), false);

    $this->get(route('adiwiyata.data'))->assertForbidden();
});

test('the correct PIN opens the real page', function () {
    unlockAdiwiyata($this)->assertRedirect(route('adiwiyata'));

    $this->get(route('adiwiyata'))
        ->assertOk()
        ->assertSee('Progres Kelengkapan Folder')
        ->assertSee(json_encode(route('adiwiyata.data')), false)
        ->assertDontSee('Masukkan PIN dari admin');

    $this->get(route('adiwiyata.data'))->assertOk();
});

test('a wrong PIN keeps the page shut', function () {
    $this->post(route('adiwiyata.unlock'), ['pin' => '000000'])
        ->assertSessionHasErrors('pin');

    $this->get(route('adiwiyata'))->assertSee('Masukkan PIN dari admin');
    $this->get(route('adiwiyata.data'))->assertForbidden();
});

test('locking again closes the page', function () {
    unlockAdiwiyata($this);
    $this->get(route('adiwiyata'))->assertSee('Progres Kelengkapan Folder');

    $this->post(route('adiwiyata.lock'))->assertRedirect();

    $this->get(route('adiwiyata'))->assertSee('Masukkan PIN dari admin');
    $this->get(route('adiwiyata.data'))->assertForbidden();
});

test('saving is refused without the PIN', function () {
    $this->postJson(route('adiwiyata.save'), ['folder_key' => 'A / B', 'status' => 'ok'])
        ->assertForbidden();

    $this->postJson(route('adiwiyata.reset'))->assertForbidden();

    expect(AdiwiyataAssessment::count())->toBe(0);
});

test('assessments saved in one session are visible in another', function () {
    unlockAdiwiyata($this);

    $this->postJson(route('adiwiyata.save'), [
        'folder_key' => '2026 / 3. Data Penggunaan air',
        'status' => 'ok',
        'note' => 'sudah diunggah',
    ])->assertOk()->assertJsonPath('status', 'ok');

    // Sesi baru = browser/perangkat lain.
    $this->flushSession();
    unlockAdiwiyata($this);

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

    unlockAdiwiyata($this)->assertSessionHasErrors('pin');
    $this->get(route('adiwiyata'))->assertSee('Masukkan PIN dari admin');
});

test('without a configured PIN nobody can get in', function () {
    config(['features.adiwiyata_pin' => '']);

    $this->get(route('adiwiyata'))
        ->assertOk()
        ->assertSee('Belum ada PIN yang diatur')
        ->assertDontSee('name="pin"', false);

    $this->post(route('adiwiyata.unlock'), ['pin' => ''])->assertNotFound();
});

test('everything adiwiyata is unreachable when the feature is off', function () {
    config(['features.adiwiyata' => false]);

    $this->get(route('adiwiyata'))->assertNotFound();
    $this->get(route('adiwiyata.data'))->assertNotFound();
    $this->post(route('adiwiyata.unlock'), ['pin' => '246810'])->assertNotFound();
    $this->get(route('home'))->assertDontSee(route('adiwiyata'), false);
});

test('the locked page is kept out of the sitemap', function () {
    $this->get(route('sitemap'))->assertDontSee(route('adiwiyata'), false);
});

test('the nav still links to it so people can reach the lock screen', function () {
    $this->get(route('home'))->assertSee(route('adiwiyata'), false);
});

test('save input is validated', function () {
    unlockAdiwiyata($this);

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
