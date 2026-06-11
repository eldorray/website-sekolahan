<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryPhoto extends Model
{
    protected $fillable = [
        'album_id',
        'image',
        'thumbnail',
        'caption',
        'order',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(GalleryAlbum::class, 'album_id');
    }

    public function imageUrl(): string
    {
        return $this->image ? asset('storage/'.$this->image) : '';
    }

    public function thumbnailUrl(): string
    {
        return $this->thumbnail ? asset('storage/'.$this->thumbnail) : $this->imageUrl();
    }
}
