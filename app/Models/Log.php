<?php

namespace App\Models;

use App\Enums\MoodEnum;
use App\Enums\SleepQualityEnum;
use App\Enums\SymptomEnum;
use App\Enums\WakeUpStateEnum;
use Illuminate\Database\Eloquent\Casts\AsEnumArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Log extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'symptoms' => AsEnumArrayObject::class.':'.SymptomEnum::class,
        'mood' => AsEnumArrayObject::class.':'.MoodEnum::class,
        'sleep_quality' => AsEnumArrayObject::class.':'.SleepQualityEnum::class,
        'wake_up_state' => AsEnumArrayObject::class.':'.WakeUpStateEnum::class,
    ];

    public function food(): HasMany
    {
        return $this->hasMany(LogFood::class);
    }
}
