<?php

use App\Services\GeoIp;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(fn () => Cache::flush());

test('geoip does not call the third party when disabled', function () {
    config(['services.geoip.enabled' => false]);
    Http::fake();

    $result = GeoIp::lookup('8.8.8.8');

    Http::assertNothingSent();
    expect($result['country_code'])->toBe('XX');
});

test('geoip queries the provider when enabled', function () {
    config(['services.geoip.enabled' => true]);
    Http::fake([
        'ipapi.co/*' => Http::response([
            'country_name' => 'Indonesia',
            'country_code' => 'ID',
        ]),
    ]);

    $result = GeoIp::lookup('8.8.8.8');

    Http::assertSent(fn ($request) => str_contains($request->url(), 'ipapi.co'));
    expect($result['country'])->toBe('Indonesia')
        ->and($result['country_code'])->toBe('ID');
});
