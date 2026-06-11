<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'visit_date',
        'participants',
        'purpose',
        'status',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];
}
