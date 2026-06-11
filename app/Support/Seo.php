<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Str;

/**
 * Request-scoped holder for per-page SEO metadata. Public pages call set()
 * during render(); the layout head reads the resolved values (with sensible
 * site-wide defaults) to emit <title> and Open Graph / Twitter tags.
 */
class Seo
{
    public ?string $title = null;

    public ?string $description = null;

    public ?string $image = null;

    public string $type = 'website';

    public function set(?string $title = null, ?string $description = null, ?string $image = null, ?string $type = null): static
    {
        if ($title !== null) {
            $this->title = $title;
        }
        if ($description !== null) {
            $this->description = $description;
        }
        if ($image !== null) {
            $this->image = $image;
        }
        if ($type !== null) {
            $this->type = $type;
        }

        return $this;
    }

    public function schoolName(): string
    {
        return Setting::get('school_name', 'Sekolah');
    }

    public function fullTitle(): string
    {
        $school = $this->schoolName();

        return $this->title ? "{$this->title} — {$school}" : $school;
    }

    public function metaDescription(): string
    {
        $raw = $this->description ?: Setting::get('hero_subtitle', '');

        return Str::limit(trim(strip_tags($raw)), 160);
    }

    public function ogImage(): ?string
    {
        return $this->image ?: Setting::imageUrl('og_image') ?: Setting::imageUrl('logo');
    }
}
