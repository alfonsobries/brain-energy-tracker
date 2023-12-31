<?php

namespace App\Enums;

use App\Notifications\AskUserMoodNotification;
use App\Notifications\AskUserSleepQualityNotification;
use App\Notifications\AskUserWakeUpStateNotification;
use App\Notifications\TelegramQuestion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

enum QuestionsEnum: string
{
    case MOOD = 'mood';

    case SLEEP_QUALITY = 'sleep-quality';

    case WAKE_UP_STATE = 'wake-up-state';

    public function index(): int
    {
        return match ($this) {
            self::MOOD => 0,
            self::SLEEP_QUALITY => 1,
            self::WAKE_UP_STATE => 2,
        };
    }

    public static function fromIndex(int $index): QuestionsEnum
    {
        return match ($index) {
            0 => self::MOOD,
            1 => self::SLEEP_QUALITY,
            2 => self::WAKE_UP_STATE,
        };
    }

    public static function fromQuestion(string $question): ?QuestionsEnum
    {
        return match ($question) {
            AskUserMoodNotification::question() => self::MOOD,
            AskUserSleepQualityNotification::question() => self::SLEEP_QUALITY,
            AskUserWakeUpStateNotification::question() => self::WAKE_UP_STATE,
            default => null,
        };
    }

    public function notification(): TelegramQuestion
    {
        return match ($this) {
            self::MOOD => new AskUserMoodNotification(),
            self::SLEEP_QUALITY => new AskUserSleepQualityNotification(),
            self::WAKE_UP_STATE => new AskUserWakeUpStateNotification(),
        };
    }

    public function answerEnum(): string
    {
        return match ($this) {
            self::MOOD => MoodEnum::class,
            self::SLEEP_QUALITY => SleepQualityEnum::class,
            self::WAKE_UP_STATE => WakeUpStateEnum::class,
        };
    }

    public function nextQuestion(): ?TelegramQuestion
    {
        $index = $this->index();

        if ($index === 2) {
            return null;
        }

        return self::fromIndex($index + 1)->notification();
    }

    public function cacheKey(string $conversationId): string
    {
        return sprintf('%s-%s', $this->value, $conversationId);
    }

    public function storeAnswerInCache(string $conversationId, string $answerText): void
    {
        $cacheKey = $this->cacheKey($conversationId);

        $answer = $this->answerEnum()::fromString($answerText);

        if ($answer === null) {
            throw new \Exception('Invalid answer');
        }

        $prevAnswers = Cache::get($cacheKey);

        if ($prevAnswers) {
            $answers = json_decode($prevAnswers, true);
            $answers[] = $answer->value;
        } else {
            $answers = [$answer->value];
        }

        Cache::put(
            key: $cacheKey,
            value: json_encode(array_unique($answers)),
            ttl: Carbon::now()->addHours(12)
        );

        info('Answer stored in cache', ['cache_key' => $cacheKey, 'answers' => array_unique($answers)]);
    }
}
