<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;
use App\Enums\WaterAmountEnum;
use App\Models\User;
use NotificationChannels\Telegram\TelegramBase;

class AskWater extends TelegramQuestion
{
    public static function question(): string
    {
        return __('How much water did you drink?');
    }

    public static function name(): QuestionsEnum
    {
        return QuestionsEnum::WATER;
    }

    public function toTelegram(User $notifiable): TelegramBase
    {
        $message = parent::toTelegram($notifiable);

        return $this->withOptions($message, WaterAmountEnum::cases());
    }
}
