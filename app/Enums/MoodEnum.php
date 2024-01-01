<?php

namespace App\Enums;

use App\Enums\Traits\FromStringTrait;

enum MoodEnum: string
{
    use FromStringTrait;

    case HAPPY = 'happy';
    case SAD = 'sad';
    case EUPHORIC = 'euphoric';
    case DEPRESSED = 'depressed';
    case ANXIOUS = 'anxious';
    case CALM = 'calm';
    case ANGRY = 'angry';
    case MOTIVATED = 'motivated';
    case TIRED = 'tired';

    public function emoji(): string
    {
        return match ($this) {
            self::HAPPY => '😀',
            self::SAD => '😢',
            self::EUPHORIC => '😍',
            self::DEPRESSED => '😞',
            self::ANXIOUS => '😰',
            self::CALM => '😌',
            self::ANGRY => '😡',
            self::MOTIVATED => '🤩',
            self::TIRED => '😴',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::HAPPY => 'Happy',
            self::SAD => 'Sad',
            self::EUPHORIC => 'Euphoric',
            self::DEPRESSED => 'Depressed',
            self::ANXIOUS => 'Anxious',
            self::CALM => 'Calm',
            self::ANGRY => 'Angry',
            self::MOTIVATED => 'Motivated',
            self::TIRED => 'Tired',
        };
    }
}
