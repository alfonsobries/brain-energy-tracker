<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

abstract class TelegramQuestion extends Notification implements ShouldQueue
{
    use Queueable;

    abstract public static function question(): string;

    abstract public static function name(): QuestionsEnum;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['telegram'];
    }

    public function withOptions(TelegramBase $message, array $options): TelegramBase
    {
        foreach ($options as $option) {
            $message->buttonWithCallback(sprintf('%s %s', $option->emoji(), $option->description()), $option->value);
        }

        return $message;
    }

    public function toTelegram(User $notifiable): TelegramBase
    {
        // $enum = $this->name();

        $message = TelegramMessage::create();

        $message->to($notifiable->telegram_user_id);

        $message->line($this->question());

        return $message;
    }
}
