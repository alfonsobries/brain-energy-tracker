<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;

class AskSugar extends TelegramQuestion
{
    public static function question(): string
    {
        return __('Whats your sugar blood level?');
    }

    public static function name(): QuestionsEnum
    {
        return QuestionsEnum::SUGAR;
    }
}
