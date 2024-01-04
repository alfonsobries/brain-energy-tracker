<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramFile;

class TelegramReadyNotification extends TelegramNotification
{
    public function toTelegram(User $notifiable): TelegramBase
    {
        $content = sprintf("*%s*\n\n%s", 'Setup successful! ✈️ ↔️  🧠⚡️', 'Your integration on Telegram has been completed successfully. Hurray!');

        return TelegramFile::create()
            ->to($notifiable->telegram_user_id)
            ->file(resource_path('assets/successkid.jpeg'), 'photo')
            ->content($content);
    }
}
