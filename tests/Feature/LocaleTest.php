<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;
use function Pest\Laravel\withSession;

uses(RefreshDatabase::class);

test('default locale is indonesian', function () {
    get('/')->assertOk();
    expect(app()->getLocale())->toBe('id');
});

test('session locale switches app locale to english', function () {
    withSession(['locale' => 'en'])->get('/')->assertOk();
    expect(app()->getLocale())->toBe('en');
});

test('invalid session locale falls back to default', function () {
    withSession(['locale' => 'zz'])->get('/')->assertOk();
    expect(app()->getLocale())->toBe('id');
});

test('switch route sets session locale and redirects back', function () {
    get('/lang/en', ['referer' => url('/tentang-kami')])
        ->assertRedirect('/tentang-kami')
        ->assertSessionHas('locale', 'en');
});

test('switch route rejects unsupported locale', function () {
    get('/lang/zz')->assertNotFound();
});

test('nav renders english when locale is en', function () {
    withSession(['locale' => 'en'])->get('/')->assertOk()->assertSee('Home');
});

test('nav renders indonesian by default', function () {
    get('/')->assertOk()->assertSee('Beranda');
});
