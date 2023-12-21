<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramFile;

class TelegramReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['telegram'];

    }

    public function toTelegram(User $notifiable): TelegramBase
    {
        $content = sprintf("*%s*\n\n%s", 'Setup successful! ✈️ ↔️  🧠⚡️', 'Your integration on Telegram has been completed successfully. Hurray!');

        return TelegramFile::create()
            ->to($notifiable->telegram_user_id)
            ->file(resource_path('assets/successkid.jpeg'), 'photo')
            ->content($content);
    }
}
