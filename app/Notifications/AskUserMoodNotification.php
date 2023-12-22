<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\MoodEnum;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramPoll;

class AskUserMoodNotification extends Notification implements ShouldQueue
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
        $choices = collect(MoodEnum::values())->map(
            fn (MoodEnum $mood) => sprintf('%s %s', $mood->emoji(), $mood->description())
        )->toArray();

        return TelegramPoll::create()
            ->to($notifiable->telegram_user_id)
            ->question('How are you feeling today?')
            ->choices($choices);
    }
}
