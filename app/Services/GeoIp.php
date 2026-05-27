<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoIp
{
    /**
     * Resolve country information from an IP address.
     * Returns ['country' => string, 'country_code' => string] or null on failure.
     * Cached per IP for 24 hours.
     */
    public static function lookup(string $ip): ?array
    {
        // Skip private/local IPs.
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return [
                'country' => 'Local',
                'country_code' => 'XX',
            ];
        }

        return Cache::remember('geoip.'.$ip, now()->addDay(), function () use ($ip) {
            try {
                // ipapi.co — free tier, no key, ~1000 req/day per IP source.
                $response = Http::timeout(2)
                    ->retry(1, 100)
                    ->get("https://ipapi.co/{$ip}/json/");

                if ($response->failed()) {
                    return null;
                }

                $data = $response->json();
                if (! is_array($data) || empty($data['country_name'] ?? null)) {
                    return null;
                }

                return [
                    'country' => (string) $data['country_name'],
                    'country_code' => (string) ($data['country_code'] ?? 'XX'),
                ];
            } catch (\Throwable $e) {
                Log::warning('GeoIp lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);

                return null;
            }
        });
    }

    /**
     * Convert ISO country code (e.g. "ID") to its emoji flag.
     */
    public static function flag(?string $code): string
    {
        if (! $code || strlen($code) !== 2) {
            return '🏳️';
        }

        $code = strtoupper($code);
        $offset = 127397; // Regional indicator base offset.

        return mb_chr(ord($code[0]) + $offset).mb_chr(ord($code[1]) + $offset);
    }
}
