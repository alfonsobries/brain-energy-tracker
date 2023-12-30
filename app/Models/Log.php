<?php

namespace App\Models;

use App\Enums\MoodEnum;
use App\Enums\SleepQualityEnum;
use App\Enums\WakeUpStateEnum;
use Illuminate\Database\Eloquent\Casts\AsEnumArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $casts = [
        'symptoms' => 'array',
        'mood' => AsEnumArrayObject::class.':'.MoodEnum::class,
        'sleep_quality' => AsEnumArrayObject::class.':'.SleepQualityEnum::class,
        'wake_up_state' => AsEnumArrayObject::class.':'.WakeUpStateEnum::class,
    ];
}
