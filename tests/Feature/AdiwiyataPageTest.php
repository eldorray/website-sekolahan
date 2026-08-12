<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('adiwiyata page renders and is linked from the public nav when enabled', function () {
    config(['features.adiwiyata' => true]);

    $this->get(route('adiwiyata'))
        ->assertOk()
        ->assertSee('Monitoring Kelengkapan Dokumen Adiwiyata')
        // @json() escapes slashes, so compare against the encoded form.
        ->assertSee(json_encode(route('adiwiyata.data')), false);

    $this->get(route('adiwiyata.data'))->assertOk();
    $this->get(route('home'))->assertSee(route('adiwiyata'), false);
    $this->get(route('sitemap'))->assertSee(route('adiwiyata'), false);
});

test('adiwiyata is invisible and unreachable when disabled', function () {
    config(['features.adiwiyata' => false]);

    $this->get(route('adiwiyata'))->assertNotFound();
    $this->get(route('adiwiyata.data'))->assertNotFound();
    $this->get(route('home'))->assertDontSee(route('adiwiyata'), false);
    $this->get(route('sitemap'))->assertDontSee(route('adiwiyata'), false);
});

test('scan snapshot is valid json with a drive-linked root', function () {
    $tree = json_decode(file_get_contents(resource_path('data/adiwiyata-tree.json')), true);

    expect($tree['root']['id'])->not->toBeEmpty()
        ->and($tree['root']['c'])->not->toBeEmpty();
});
