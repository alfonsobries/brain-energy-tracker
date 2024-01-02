<?php

namespace App\Enums;

enum CommandEnum: string
{
    case START_SESSION = 'startsession';

    case FINISH_SESSION = 'finish';

    case DINNER = 'dinner';

    case BREAKFAST = 'breakfast';

    case LUNCH = 'lunch';

    case SNACK = 'snack';

    case MOOD = 'mood';

    case SLEEP_QUALITY = 'sleepquality';

    case WAKE_UP_STATE = 'wakeupstate';

    case SYMPTOMS = 'symptoms';

    public function description(): string
    {
        return match ($this) {
            self::START_SESSION => 'Start prompt session',
            self::FINISH_SESSION => 'Finish prompt session and store answers',
            self::DINNER => 'Register your dinner',
            self::BREAKFAST => 'Register your breakfast',
            self::LUNCH => 'Register your lunch',
            self::SNACK => 'Register a snack',
            self::SYMPTOMS => 'Register your symptoms',
            self::SLEEP_QUALITY => 'Register your sleep quality',
            self::WAKE_UP_STATE => 'Register your wake up state',
            self::MOOD => 'Register your day mood',
        };
    }

    public static function fromString(string $command): ?CommandEnum
    {
        return match ($command) {
            '/startsession' => self::START_SESSION,
            '/finish' => self::FINISH_SESSION,
            '/dinner' => self::DINNER,
            '/breakfast' => self::BREAKFAST,
            '/lunch' => self::LUNCH,
            '/snack' => self::SNACK,
            '/symptoms' => self::SYMPTOMS,
            '/sleepquality' => self::SLEEP_QUALITY,
            '/wakeupstate' => self::WAKE_UP_STATE,
            '/mood' => self::MOOD,
            default => null,
        };
    }
}
