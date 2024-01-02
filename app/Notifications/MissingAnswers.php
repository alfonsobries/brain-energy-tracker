<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;
use App\Models\User;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class MissingAnswers extends TelegramNotification
{
    public function toTelegram(User $notifiable): TelegramBase
    {
        $message = TelegramMessage::create();

        $message->to($notifiable->telegram_user_id);

        $names = $notifiable->questionsWithoutAnAnswer()->map(fn (QuestionsEnum $question) => $question->value);

        $message->content('*You have not answered the following questions:*'."\n\n".$names->implode(', '));

        return $message;
    }
}
