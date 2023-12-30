<?php

namespace App\Enums\Traits;

trait FromStringTrait
{
    public static function fromString(string $value): ?static
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return null;
    }
}
