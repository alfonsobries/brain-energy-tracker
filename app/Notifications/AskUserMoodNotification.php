<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\MoodEnum;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Telegram\TelegramPoll;

class AskUserMoodNotification extends Notification implements ShouldQueue
{
    use Queueable;

    const QUESTION = 'How are you feeling today?';

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

        $message = TelegramMessage::create()
            ->to($notifiable->telegram_user_id)
            ->line(self::QUESTION);

        foreach (MoodEnum::values() as $mood) {
            $message->buttonWithCallback(sprintf('%s %s', $mood->emoji(), $mood->description()), $mood->value);
        }

        return $message;

        // $choices = collect(MoodEnum::values())->map(
        //     fn (MoodEnum $mood) => sprintf('%s %s', $mood->emoji(), $mood->description())
        // )->toArray();

        // return TelegramPoll::create()
        //     ->to($notifiable->telegram_user_id)
        //     ->question(self::QUESTION)
        //     ->options([
        //         'allows_multiple_answers' => true,
        //     ])
        //     ->choices($choices);
    }
}
