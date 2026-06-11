<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class EventTheme extends Model
{
    protected $fillable = [
        'name',
        'style',
        'message',
        'start_date',
        'end_date',
        'primary_color',
        'secondary_color',
        'accent_color',
        'background_color',
        'background_image',
        'text_color',
        'priority',
        'repeat_annually',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'repeat_annually' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function backgroundImageUrl(): ?string
    {
        if (! $this->background_image) {
            return null;
        }

        return asset('storage/'.$this->background_image);
    }

    public static function activeFor(Carbon|string|null $date = null): ?self
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date ?? now());

        return static::query()
            ->where('is_active', true)
            ->orderByDesc('priority')
            ->orderBy('start_date')
            ->get()
            ->first(fn (self $theme) => $theme->isActiveOn($date));
    }

    public function isActiveOn(Carbon|string|null $date = null): bool
    {
        if (! $this->is_active || ! $this->start_date || ! $this->end_date) {
            return false;
        }

        $date = $date instanceof Carbon ? $date : Carbon::parse($date ?? now());

        if (! $this->repeat_annually) {
            return $date->toDateString() >= $this->start_date->toDateString()
                && $date->toDateString() <= $this->end_date->toDateString();
        }

        $today = $date->format('m-d');
        $start = $this->start_date->format('m-d');
        $end = $this->end_date->format('m-d');

        if ($start <= $end) {
            return $today >= $start && $today <= $end;
        }

        return $today >= $start || $today <= $end;
    }
}
