<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;

class AskSymptomsNotification extends TelegramQuestion
{
    public static function question(): string
    {
        return __('Please describe your symptoms');
    }

    public static function name(): QuestionsEnum
    {
        return QuestionsEnum::SYMPTOMS;
    }
}
