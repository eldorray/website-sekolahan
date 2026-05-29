<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brochure extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function previewUrl(): string
    {
        return $this->preview_image
            ? asset('storage/'.$this->preview_image)
            : 'https://picsum.photos/seed/brochure'.$this->id.'/600/800';
    }

    public function fileUrl(): ?string
    {
        return $this->file ? asset('storage/'.$this->file) : null;
    }
}
