<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdiwiyataAssessment extends Model
{
    protected $fillable = ['folder_key', 'status', 'note'];
}
