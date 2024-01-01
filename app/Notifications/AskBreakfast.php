<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;

class AskBreakfast extends TelegramQuestion
{
    public static function question(): string
    {
        return __('What did you have for breakfast?');
    }

    public static function name(): QuestionsEnum
    {
        return QuestionsEnum::BREAKFAST;
    }
}
