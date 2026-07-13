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
