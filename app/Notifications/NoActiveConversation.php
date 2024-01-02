<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class NoActiveConversation extends TelegramNotification
{
    public function toTelegram(User $notifiable): TelegramBase
    {
        return
            TelegramMessage::create()
                ->to($notifiable->telegram_user_id)
                ->content('You dont have any active conversation session. Start a new one by typing /start-session');

    }
}
