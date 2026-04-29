<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = [];

    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever('setting.' . $key, function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('setting.' . $key);
    }

    public static function all_keyed(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }

    public static function imageUrl(string $key): ?string
    {
        $path = static::get($key);
        if (! $path) return null;
        return asset('storage/' . $path);
    }
}
