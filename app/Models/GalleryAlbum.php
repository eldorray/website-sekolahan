<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GalleryAlbum extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover_image',
        'order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(GalleryPhoto::class, 'album_id')->orderBy('order')->orderByDesc('id');
    }

    public function coverUrl(): string
    {
        if ($this->cover_image) {
            return asset('storage/'.$this->cover_image);
        }

        $first = $this->photos()->first();
        if ($first) {
            return $first->thumbnailUrl();
        }

        return 'https://picsum.photos/seed/album'.$this->id.'/600/400';
    }
}
