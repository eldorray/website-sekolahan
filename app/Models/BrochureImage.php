<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrochureImage extends Model
{
    protected $fillable = [
        'brochure_id',
        'image',
        'thumbnail',
        'order',
        'is_cover',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function brochure(): BelongsTo
    {
        return $this->belongsTo(Brochure::class);
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
