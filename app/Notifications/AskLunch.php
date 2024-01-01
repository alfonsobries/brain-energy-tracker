<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;

class AskLunch extends TelegramQuestion
{
    public static function question(): string
    {
        return __('What did you have for lunch?');
    }

    public static function name(): QuestionsEnum
    {
        return QuestionsEnum::LUNCH;
    }
}
