<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brochure extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'preview_image',
        'file',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(BrochureImage::class)->orderByDesc('is_cover')->orderBy('order')->orderBy('id');
    }

    public function coverUrl(): string
    {
        $cover = $this->images->firstWhere('is_cover', true) ?? $this->images->first();
        if ($cover) {
            return $cover->thumbnailUrl();
        }

        if ($this->preview_image) {
            return asset('storage/'.$this->preview_image);
        }

        return 'https://picsum.photos/seed/brochure'.$this->id.'/600/800';
    }

    public function fileUrl(): ?string
    {
        return $this->file ? asset('storage/'.$this->file) : null;
    }
}
