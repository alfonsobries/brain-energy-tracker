<?php

namespace App\Enums;

use App\Enums\Traits\FromStringTrait;

enum SleepQualityEnum: string
{
    use FromStringTrait;

    case WELL = 'well';
    case POORLY = 'poorly';
    case RESTLESSLY = 'restlessly';
    case SOUNDLY = 'soundly';
    case BRIEFLY = 'briefly';
    case INTERMITTENTLY = 'intermittently';
    case PEACEFULLY = 'peacefully';
    case FITFULLY = 'fitfully';
    case UNCOMFORTABLY = 'uncomfortably';
    case NIGHTMARES = 'nightmares';

    public function emoji(): string
    {
        return match ($this) {
            self::WELL => '😊',
            self::POORLY => '😟',
            self::RESTLESSLY => '😬',
            self::SOUNDLY => '😴',
            self::BRIEFLY => '⏲️',
            self::INTERMITTENTLY => '🔛🔝',
            self::PEACEFULLY => '🕊️',
            self::FITFULLY => '😓',
            self::UNCOMFORTABLY => '😣',
            self::NIGHTMARES => '😱',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::WELL => 'Well',
            self::POORLY => 'Poorly',
            self::RESTLESSLY => 'Restlessly',
            self::SOUNDLY => 'Soundly',
            self::BRIEFLY => 'Briefly',
            self::INTERMITTENTLY => 'Intermittently',
            self::PEACEFULLY => 'Peacefully',
            self::FITFULLY => 'Fitfully',
            self::UNCOMFORTABLY => 'Uncomfortably',
            self::NIGHTMARES => 'Nightmares',
        };
    }

    public static function values(): array
    {
        return [
            self::WELL,
            self::POORLY,
            self::RESTLESSLY,
            self::SOUNDLY,
            self::BRIEFLY,
            self::INTERMITTENTLY,
            self::PEACEFULLY,
            self::FITFULLY,
            self::UNCOMFORTABLY,
            self::NIGHTMARES,
        ];
    }

    public static function fromString(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return null;
    }
}
