<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class AllQuestionsAnswered extends TelegramNotification
{
    public function toTelegram(User $notifiable): TelegramBase
    {
        return
            TelegramMessage::create()
                ->to($notifiable->telegram_user_id)
                ->content('All questions answered. Use `/finish` if you want to store the values now.');

    }
}
