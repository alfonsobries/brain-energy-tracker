<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;

class AskSnack extends TelegramQuestion
{
    public static function question(): string
    {
        return __('Did you have any other food/snacks?');
    }

    public static function name(): QuestionsEnum
    {
        return QuestionsEnum::SNACK;
    }
}
