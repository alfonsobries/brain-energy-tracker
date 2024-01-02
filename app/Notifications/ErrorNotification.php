<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class ErrorNotification extends TelegramNotification
{
    public function __construct(private string $message)
    {
    }

    public function toTelegram(User $notifiable): TelegramBase
    {
        return
            TelegramMessage::create()
                ->to($notifiable->telegram_user_id)
                ->content('*🚨 Error:*'."\n\n".$this->message);

    }
}
