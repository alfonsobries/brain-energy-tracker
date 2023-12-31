<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\QuestionsEnum;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
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

    private function questionCacheKey($conversationId): string
    {
        $name = $this->name();

        return $name->cacheKey($conversationId).'-'.$name->value;
    }

    public function notAskedYet(string $conversationId): bool
    {
        return Cache::missing($this->questionCacheKey($conversationId));
    }

    public function withOptions(TelegramBase $message, array $options): TelegramBase
    {
        foreach ($options as $option) {
            $message->buttonWithCallback(sprintf('%s %s', $option->emoji(), $option->description()), $option->value);
        }

        return $message;
    }

    public function storeQuestionAsked(string $conversationId): void
    {
        $cacheKey = $this->questionCacheKey($conversationId);

        Cache::put($cacheKey, true, now()->addHours(12));
    }

    public function toTelegram(User $notifiable): TelegramBase
    {
        $this->storeQuestionAsked($notifiable->getConversationId());

        $message = TelegramMessage::create();

        $message->to($notifiable->telegram_user_id);

        $message->line($this->question());

        return $message;
    }
}
