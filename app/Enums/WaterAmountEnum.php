<?php

namespace App\Enums;

use App\Enums\Traits\FromStringTrait;

enum WaterAmountEnum: string
{
    use FromStringTrait;

    case NONE = 'none';
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function emoji(): string
    {
        return match ($this) {
            self::NONE => '🚫',
            self::LOW => '💧',
            self::MEDIUM => '💧💧',
            self::HIGH => '💧💧💧',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NONE => 'None',
            self::LOW => '1-2 glasses',
            self::MEDIUM => '3-4 glasses',
            self::HIGH => '5+ glasses',
        };
    }
}
