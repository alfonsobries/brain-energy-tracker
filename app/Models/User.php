<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\QuestionsEnum;
use App\Notifications\TelegramQuestion;
use Carbon\Carbon;
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
        Cache::put(
            key: $this->conversationKey(),
            value: uniqid(),
            ttl: Carbon::now()->addHours(12)
        );

        $this->ask(QuestionsEnum::fromIndex(0)->notification());
    }

    public function getConversationId(): ?string
    {
        return Cache::get($this->conversationKey());
    }

    public function ask(TelegramQuestion $question): void
    {
        $this->notifyNow($question);
    }
}
