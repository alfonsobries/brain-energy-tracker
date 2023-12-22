<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogFood extends Model
{
    use HasFactory;

    protected $casts = [
        'main_ingredients' => 'array',
    ];
}
