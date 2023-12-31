<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;
use App\Enums\WakeUpStateEnum;
use App\Models\User;
use NotificationChannels\Telegram\TelegramBase;

class AskUserWakeUpStateNotification extends TelegramQuestion
{
    public static function question(): string
    {
        return __('How did you wake up this morning?');
    }

    public static function name(): QuestionsEnum
    {
        return QuestionsEnum::WAKE_UP_STATE;
    }

    public function toTelegram(User $notifiable): TelegramBase
    {
        $message = parent::toTelegram($notifiable);

        return $this->withOptions($message, WakeUpStateEnum::values());
    }
}
