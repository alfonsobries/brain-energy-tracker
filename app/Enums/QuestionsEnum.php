<?php

namespace App\Enums;

use App\Notifications\AskSymptomsNotification;
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

    case SYMPTOMS = 'symptoms';

    public function index(): int
    {
        return match ($this) {
            self::MOOD => 0,
            self::SLEEP_QUALITY => 1,
            self::WAKE_UP_STATE => 2,
            self::SYMPTOMS => 3,
        };
    }

    public static function fromIndex(int $index): ?QuestionsEnum
    {
        return match ($index) {
            0 => self::MOOD,
            1 => self::SLEEP_QUALITY,
            2 => self::WAKE_UP_STATE,
            3 => self::SYMPTOMS,
            default => null,
        };
    }

    public static function fromQuestion(string $question): ?QuestionsEnum
    {
        return match ($question) {
            AskUserMoodNotification::question() => self::MOOD,
            AskUserSleepQualityNotification::question() => self::SLEEP_QUALITY,
            AskUserWakeUpStateNotification::question() => self::WAKE_UP_STATE,
            AskSymptomsNotification::question() => self::SYMPTOMS,
            default => null,
        };
    }

    public static function lastAsked(string $conversationId): ?QuestionsEnum
    {
        for ($index = 3; $index >= 0; $index--) {
            /**
             * @var QuestionsEnum $question
             */
            $question = self::fromIndex($index);

            if ($question?->notification()->alreadyAsked($conversationId)) {
                return $question;
            }
        }

        return null;

    }

    public function notification(): TelegramQuestion
    {
        return match ($this) {
            self::MOOD => new AskUserMoodNotification(),
            self::SLEEP_QUALITY => new AskUserSleepQualityNotification(),
            self::WAKE_UP_STATE => new AskUserWakeUpStateNotification(),
            self::SYMPTOMS => new AskSymptomsNotification(),
        };
    }

    public function answerEnum(): ?string
    {
        return match ($this) {
            self::MOOD => MoodEnum::class,
            self::SLEEP_QUALITY => SleepQualityEnum::class,
            self::WAKE_UP_STATE => WakeUpStateEnum::class,
            // not applicable
            // self::SYMPTOMS => SymptomsEnum::class,
            default => null,
        };
    }

    public function nextQuestion(): ?TelegramQuestion
    {
        $index = $this->index();

        $question = self::fromIndex($index + 1);

        return $question?->notification();
    }

    public function cacheKey(string $conversationId): string
    {
        return sprintf('%s-%s', $this->value, $conversationId);
    }

    public function storeAnswerInCache(string $conversationId, string $answerText): void
    {
        $cacheKey = $this->cacheKey($conversationId);

        $answerEnum = $this->answerEnum();

        if ($answerEnum !== null) {
            $answer = $answerEnum::fromString($answerText);

            if ($answer === null) {
                throw new \Exception('Invalid answer');
            }

            $answerText = $answer->value;
        }

        $prevAnswers = Cache::get($cacheKey);

        if ($prevAnswers) {
            $answers = json_decode($prevAnswers, true);
            $answers[] = $answerText;
        } else {
            $answers = [$answerText];
        }

        Cache::put(
            key: $cacheKey,
            value: json_encode(array_unique($answers)),
            ttl: Carbon::now()->addHours(12)
        );

        info('Answer stored in cache', ['cache_key' => $cacheKey, 'answers' => $answers]);
    }
}
