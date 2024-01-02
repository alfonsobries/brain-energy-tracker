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
use App\Notifications\AskWater;
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

    case WATER = 'water';

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
            self::WATER => 8,
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
            8 => self::WATER,
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
            AskWater::question() => self::WATER,
            default => null,
        };
    }

    public static function fromString(string $question): ?QuestionsEnum
    {
        return match ($question) {
            'mood' => self::MOOD,
            'sleep-quality' => self::SLEEP_QUALITY,
            'wake-up-state' => self::WAKE_UP_STATE,
            'symptoms' => self::SYMPTOMS,
            'breakfast' => self::BREAKFAST,
            'lunch' => self::LUNCH,
            'dinner' => self::DINNER,
            'snack' => self::SNACK,
            'water' => self::WATER,
            default => null,
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::MOOD => 'Mood',
            self::SLEEP_QUALITY => 'Sleep Quality',
            self::WAKE_UP_STATE => 'Wake Up State',
            self::SYMPTOMS => 'Symptoms',
            self::BREAKFAST => 'Breakfast',
            self::LUNCH => 'Lunch',
            self::DINNER => 'Dinner',
            self::SNACK => 'Snack',
            self::WATER => 'Water',
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
            self::WATER => new AskWater(),
        };
    }

    public function time(): string
    {
        return match ($this) {
            self::SLEEP_QUALITY => '08:00',
            self::WAKE_UP_STATE => '08:00',
            self::SYMPTOMS => '14:00',
            self::BREAKFAST => '10:00',
            self::LUNCH => '16:00',
            self::MOOD => '19:00',
            self::SNACK => '20:00',
            self::DINNER => '21:00',
            self::WATER => '21:15',
        };
    }

    public function answerEnum(): ?string
    {
        return match ($this) {
            self::MOOD => MoodEnum::class,
            self::SLEEP_QUALITY => SleepQualityEnum::class,
            self::WAKE_UP_STATE => WakeUpStateEnum::class,
            self::SYMPTOMS => SymptomEnum::class,
            self::WATER => WaterAmountEnum::class,
            // not applicable
            // self::BREAKFAST => null,
            // self::LUNCH => null,
            // self::DINNER => null,
            // self::SNACK => null,
            default => null,
        };
    }

    public function nextQuestion(): ?QuestionsEnum
    {
        $index = $this->index();

        $question = self::fromIndex($index + 1);

        return $question;
    }

    public function cacheKey(string $conversationId): string
    {
        return sprintf('%s-%s', $this->value, $conversationId);
    }

    public static function lastAsked(string $conversationId): ?QuestionsEnum
    {
        $question = Cache::get('last-question-asked.'.$conversationId);

        if ($question === null) {
            return null;
        }

        return self::fromString($question);
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

    public function storedAnswer(string $conversationId): ?string
    {
        $answers = $this->storedAnswers($conversationId);

        if (count($answers) === 0) {
            return null;
        }

        return $answers[0];
    }

    public function storeAnswerInCache(string $conversationId, string $answerText): void
    {
        $cacheKey = $this->cacheKey($conversationId);

        $answerEnum = $this->answerEnum();

        $answers = $this->storedAnswers($conversationId);

        if ($answerEnum !== null) {
            $answer = $answerEnum::fromString($answerText);

            if ($answer === null) {
                throw new \Exception('Invalid answer');
            }

            $answers[] = $answer->value;
        } else {
            $answers[] = $answerText;
        }

        Cache::put(
            key: $cacheKey,
            value: json_encode(array_unique($answers)),
            ttl: Carbon::now()->addHours(18)
        );

        info('Answer stored in cache', ['cache_key' => $cacheKey]);
    }
}
