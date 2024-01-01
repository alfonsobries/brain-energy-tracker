<?php

namespace App\Enums;

use App\Notifications\AskBreakfast;
use App\Notifications\AskDinner;
use App\Notifications\AskLunch;
use App\Notifications\AskSnack;
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

    case BREAKFAST = 'breakfast';

    case LUNCH = 'lunch';

    case DINNER = 'dinner';

    case SNACK = 'snack';

    public function index(): int
    {
        return match ($this) {
            self::SLEEP_QUALITY => 0,
            self::WAKE_UP_STATE => 1,
            self::MOOD => 2,
            self::SYMPTOMS => 3,
            self::BREAKFAST => 4,
            self::LUNCH => 5,
            self::DINNER => 6,
            self::SNACK => 7,
        };
    }

    public static function fromIndex(int $index): ?QuestionsEnum
    {
        return match ($index) {
            0 => self::SLEEP_QUALITY,
            1 => self::WAKE_UP_STATE,
            2 => self::MOOD,
            3 => self::SYMPTOMS,
            4 => self::BREAKFAST,
            5 => self::LUNCH,
            6 => self::DINNER,
            7 => self::SNACK,
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
            AskBreakfast::question() => self::BREAKFAST,
            AskLunch::question() => self::LUNCH,
            AskDinner::question() => self::DINNER,
            AskSnack::question() => self::SNACK,
            default => null,
        };
    }

    public function notification(): TelegramQuestion
    {
        return match ($this) {
            self::MOOD => new AskUserMoodNotification(),
            self::SLEEP_QUALITY => new AskUserSleepQualityNotification(),
            self::WAKE_UP_STATE => new AskUserWakeUpStateNotification(),
            self::SYMPTOMS => new AskSymptomsNotification(),
            self::BREAKFAST => new AskBreakfast(),
            self::LUNCH => new AskLunch(),
            self::DINNER => new AskDinner(),
            self::SNACK => new AskSnack(),
        };
    }

    public function answerEnum(): ?string
    {
        return match ($this) {
            self::MOOD => MoodEnum::class,
            self::SLEEP_QUALITY => SleepQualityEnum::class,
            self::WAKE_UP_STATE => WakeUpStateEnum::class,
            self::SYMPTOMS => SymptomEnum::class,
            // not applicable
            // self::BREAKFAST => null,
            // self::LUNCH => null,
            // self::DINNER => null,
            // self::SNACK => null,
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

    public static function lastAsked(string $conversationId): ?QuestionsEnum
    {
        $total = count(self::cases());

        for ($index = $total; $index >= 0; $index--) {
            /**
             * @var \App\Enums\QuestionsEnum $question
             */
            $question = self::fromIndex($index);

            if ($question?->notification()->alreadyAsked($conversationId)) {
                return $question;
            }
        }

        return null;

    }

    public function storedAnswers(string $conversationId): array
    {
        $cacheKey = $this->cacheKey($conversationId);

        $answers = Cache::get($cacheKey);

        if ($answers) {
            return json_decode($answers, true);
        }

        return [];
    }

    public function storedAnswer(string $conversationId): string
    {
        $cacheKey = $this->cacheKey($conversationId);

        return Cache::get($cacheKey) ?? '';
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

            $answers = $this->storedAnswers($conversationId);

            $answers[] = $answerText;

            $value = json_encode(array_unique($answers));
        } else {
            $value = $answerText;
        }

        Cache::put(
            key: $cacheKey,
            value: $value,
            ttl: Carbon::now()->addHours(18)
        );

        info('Answer stored in cache', ['cache_key' => $cacheKey, 'answers' => $value]);
    }
}
