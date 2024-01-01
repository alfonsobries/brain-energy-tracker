<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;
use App\Enums\SymptomEnum;
use App\Models\User;
use NotificationChannels\Telegram\TelegramBase;

class AskSymptomsNotification extends TelegramQuestion
{
    public static function question(): string
    {
        return __('what symptoms do you have?');
    }

    public static function name(): QuestionsEnum
    {
        return QuestionsEnum::SYMPTOMS;
    }

    public function toTelegram(User $notifiable): TelegramBase
    {
        $message = parent::toTelegram($notifiable);

        return $this->withOptions($message, SymptomEnum::cases());
    }
}
