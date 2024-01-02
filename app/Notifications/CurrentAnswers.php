<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;
use App\Models\User;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class CurrentAnswers extends TelegramNotification
{
    public function toTelegram(User $notifiable): TelegramBase
    {
        $message = TelegramMessage::create();

        $message->to($notifiable->telegram_user_id);

        $names = $notifiable->questionsWithAnswer()->map(fn ($value, $name) => '*'.QuestionsEnum::fromString($name)->name().'*: '.(is_array($value) ? implode(', ', $value) : $value));

        $message->content('*You have answered the following questions:*'."\n\n".$names->implode("\n"));

        return $message;
    }
}
