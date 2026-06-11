<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'ip',
        'country',
        'country_code',
        'path',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
