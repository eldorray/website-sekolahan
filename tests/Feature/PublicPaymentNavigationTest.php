<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public pages prominently expose payment outside the collapsed mobile menu', function (string $path) {
    $response = $this->get($path)->assertOk();
    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
    $xpath = new DOMXPath($document);
    $links = $xpath->query('//header//a[@data-spp-payment-cta]');

    expect($links->length)->toBe(1);
    $link = $links->item(0);
    expect($link->getAttribute('href'))->toBe(route('payments.entry'))
        ->and(trim($link->textContent))->toContain('Bayar SPP/Lainnya')
        ->and($link->getAttribute('class'))->toContain('btn-brand')
        ->and($xpath->query('ancestor::*[@x-show or @x-cloak]', $link)->length)->toBe(0);

    for ($node = $link; $node instanceof DOMElement; $node = $node->parentNode) {
        expect(preg_match('/(?:^|\s)(?:[\w-]+:)?hidden(?:\s|$)/', $node->getAttribute('class')))->toBe(0);
    }

    expect($xpath->query('//footer//a[@href="'.route('payments.entry').'"]')->length)->toBe(1);
})->with(['/', '/tentang-kami', '/kontak']);

test('payment entry is available without signing in for visitors and every account role', function (?string $role) {
    if ($role !== null) {
        $this->actingAs(User::factory()->create(['role' => $role, 'is_active' => true]));
    }

    $this->get(route('payments.entry'))->assertOk()
        ->assertSee('Bayar sekolah tanpa login')->assertSee('NISN atau nama lengkap siswa')
        ->assertDontSee('Gunakan akun orang tua');
})->with([null, 'admin', 'orang_tua', 'guru']);

test('financial panels remain restricted to the appropriate authenticated role', function (string $role, string $destination) {
    $user = User::factory()->create(['role' => $role, 'is_active' => true]);
    $this->actingAs($user)->get(route($destination))->assertOk();
})->with([['admin', 'admin.spp'], ['orang_tua', 'orang-tua.spp']]);

test('public payment entry does not grant teachers access to protected financial pages', function () {
    $user = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $this->actingAs($user)->get('/bayar-spp')->assertOk();
    $this->get(route('orang-tua.spp'))->assertForbidden();
    $this->get(route('admin.spp'))->assertForbidden();
});

test('visiting public payments does not require or create a login session', function () {
    $this->get(route('payments.entry'))->assertOk()->assertSessionMissing('url.intended');
    $this->assertGuest();
    $this->get(route('orang-tua.spp'))->assertRedirect(route('login'));
    $this->get(route('admin.spp'))->assertRedirect(route('login'));
});
