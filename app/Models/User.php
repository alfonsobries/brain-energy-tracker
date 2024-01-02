<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\QuestionsEnum;
use App\Jobs\GetFoodLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function telegramCommand(): string
    {
        return Cache::rememberForever('telegram-instruction', function () {
            return sprintf('/start %s', encrypt('tracker:'.$this->id));
        });
    }

    public function conversationKey(): string
    {
        return 'telegram-conversation-'.$this->id;
    }

    public function startConversation(): void
    {
        Cache::rememberForever($this->conversationKey(), fn () => uniqid());
    }

    public function finishConversation(): void
    {
        $this->storePreviousAnwers();
    }

    public function getConversationId(): ?string
    {
        return Cache::get($this->conversationKey());
    }

    public function ask(QuestionsEnum $question): void
    {
        Cache::put('last-question-asked.'.$this->getConversationId(), $question->value, now()->addHours(24));

        $this->notifyNow($question->notification());
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function allQuestionsAnswered(): bool
    {
        $conversationId = $this->getConversationId();

        if ($conversationId === null) {
            return true;
        }

        $answers = [
            QuestionsEnum::MOOD->storedAnswers($conversationId),
            QuestionsEnum::SLEEP_QUALITY->storedAnswers($conversationId),
            QuestionsEnum::WAKE_UP_STATE->storedAnswers($conversationId),
            QuestionsEnum::SYMPTOMS->storedAnswers($conversationId),
            QuestionsEnum::BREAKFAST->storedAnswer($conversationId),
            QuestionsEnum::LUNCH->storedAnswer($conversationId),
            QuestionsEnum::DINNER->storedAnswer($conversationId),
            QuestionsEnum::SNACK->storedAnswer($conversationId),
        ];

        return count(array_filter($answers)) === count($answers);
    }

    public function storePreviousAnwers(): void
    {
        $conversationId = $this->getConversationId();

        if ($conversationId === null) {
            Cache::forget($this->conversationKey());

            return;
        }

        $log = $this->logs()->create([
            'mood' => QuestionsEnum::MOOD->storedAnswers($conversationId),
            'sleep_quality' => QuestionsEnum::SLEEP_QUALITY->storedAnswers($conversationId),
            'wake_up_state' => QuestionsEnum::WAKE_UP_STATE->storedAnswers($conversationId),
            'symptoms' => QuestionsEnum::SYMPTOMS->storedAnswers($conversationId),
        ]);

        $template = <<<'EOT'
Breakfast: %s

Lunch: %s

Dinner: %s

Snack: %s

EOT;

        $foods = [
            QuestionsEnum::BREAKFAST->storedAnswer($conversationId),
            QuestionsEnum::LUNCH->storedAnswer($conversationId),
            QuestionsEnum::DINNER->storedAnswer($conversationId),
            QuestionsEnum::SNACK->storedAnswer($conversationId),
        ];

        if (count(array_filter($foods)) > 0) {
            $foodDescription = sprintf(
                $template,
                ...$foods
            );

            GetFoodLog::dispatch($log, $foodDescription);
        }

        Cache::forget($this->conversationKey());
    }
}
