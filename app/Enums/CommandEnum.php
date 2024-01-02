<?php

namespace App\Enums;

enum CommandEnum: string
{
    case BREAKFAST = 'breakfast';

    case LUNCH = 'lunch';

    case DINNER = 'dinner';

    case SNACK = 'snack';

    case WATER = 'water';

    case MOOD = 'mood';

    case SLEEP_QUALITY = 'sleepquality';

    case WAKE_UP_STATE = 'wakeupstate';

    case SYMPTOMS = 'symptoms';

    case FINISH_SESSION = 'finish';

    case RESTART_SESSION = 'restart';

    case GET_MISSING = 'missing';

    public function description(): string
    {
        return match ($this) {
            self::DINNER => 'Register your dinner',
            self::BREAKFAST => 'Register your breakfast',
            self::LUNCH => 'Register your lunch',
            self::SNACK => 'Register a snack',
            self::WATER => 'Register your water intake',
            self::SYMPTOMS => 'Register your symptoms',
            self::SLEEP_QUALITY => 'Register your sleep quality',
            self::WAKE_UP_STATE => 'Register your wake up state',
            self::MOOD => 'Register your day mood',
            self::RESTART_SESSION => 'Restart prompt session without storing answers',
            self::FINISH_SESSION => 'Finish prompt session and store answers',
            self::GET_MISSING => 'Get missing answers',
        };
    }

    public static function fromString(string $command): ?CommandEnum
    {
        return match ($command) {
            '/restart' => self::RESTART_SESSION,
            '/missing' => self::GET_MISSING,
            '/finish' => self::FINISH_SESSION,
            '/dinner' => self::DINNER,
            '/breakfast' => self::BREAKFAST,
            '/lunch' => self::LUNCH,
            '/snack' => self::SNACK,
            '/water' => self::WATER,
            '/symptoms' => self::SYMPTOMS,
            '/sleepquality' => self::SLEEP_QUALITY,
            '/wakeupstate' => self::WAKE_UP_STATE,
            '/mood' => self::MOOD,
            default => null,
        };
    }
}
