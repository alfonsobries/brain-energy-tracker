<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;
use App\Enums\SleepQualityEnum;
use App\Models\User;
use NotificationChannels\Telegram\TelegramBase;

class AskUserSleepQualityNotification extends TelegramQuestion
{
    public static function question(): string
    {
        return __('How did you sleep last night?');
    }

    public static function name(): QuestionsEnum
    {
        return QuestionsEnum::SLEEP_QUALITY;
    }

    public function toTelegram(User $notifiable): TelegramBase
    {
        $message = parent::toTelegram($notifiable);

        return $this->withOptions($message, SleepQualityEnum::values());
    }
}
