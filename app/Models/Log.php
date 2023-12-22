<?php

namespace App\Models;

use App\Enums\MoodEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $casts = [
        'symptoms' => 'array',
        'mood' => MoodEnum::class,
    ];
}
