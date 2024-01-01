<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;

class AskDinner extends TelegramQuestion
{
    public static function question(): string
    {
        return __('What did you have for dinner?');
    }

    public static function name(): QuestionsEnum
    {
        return QuestionsEnum::DINNER;
    }
}
