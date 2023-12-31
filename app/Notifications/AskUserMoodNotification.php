<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\MoodEnum;
use App\Enums\QuestionsEnum;
use App\Models\User;
use NotificationChannels\Telegram\TelegramBase;

class AskUserMoodNotification extends TelegramQuestion
{
    public static function question(): string
    {
        return __('How did you felt today?');
    }

    public static function name(): QuestionsEnum
    {
        return QuestionsEnum::MOOD;
    }

    public function toTelegram(User $notifiable): TelegramBase
    {
        $message = parent::toTelegram($notifiable);

        return $this->withOptions($message, MoodEnum::values());
    }
}
