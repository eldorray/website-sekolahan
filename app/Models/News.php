<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $table = 'news';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'image',
        'published_at',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'date',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function imageUrl(): string
    {
        if ($this->image && file_exists(public_path('storage/'.$this->image))) {
            return asset('storage/'.$this->image);
        }

        return 'https://picsum.photos/seed/news'.$this->id.'/600/400';
    }
}
