<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MoodEnum;
use App\Enums\SleepQualityEnum;
use App\Enums\WakeUpStateEnum;
use App\Models\User;
use App\Notifications\AskUserMoodNotification;
use App\Notifications\AskUserSleepQualityNotification;
use App\Notifications\AskUserWakeUpStateNotification;
use App\Notifications\TelegramReadyNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TelegramBotWebhookController extends Controller
{
    const SLEEP_QUALITY = 'sleep-quality';

    const WAKE_UP_STATE = 'wake-up-state';

    const MOOD = 'mood';

    public function __invoke(Request $request): JsonResponse
    {
        $telegramUserId = Arr::get($request->all(), 'message.from.id', Arr::get($request->all(), 'callback_query.from.id'));

        if ($telegramUserId === null) {
            return response()->json(['status' => 'no-telegram-user-id']);
        }

        $user = User::where('telegram_user_id', $telegramUserId)->first();

        if ($user === null) {
            Log::info('User not found', ['telegram_user_id' => $telegramUserId]);

            return $this->tryToAssignTelegramIdToUser($telegramUserId);
        }

        $conversationId = $user->getConversationId();

        if ($conversationId === null) {
            Log::info('No conversation ID', ['telegram_user_id' => $telegramUserId]);

            return response()->json(['status' => 'no-conversation-id']);
        }

        if ($this->isMoodAnswer($request)) {
            $response = $this->storeConversationValue(self::MOOD, MoodEnum::class, $request, $user, $conversationId);

            if ($this->haventAsked(self::WAKE_UP_STATE, $conversationId)) {
                $user->notifyNow(new AskUserWakeUpStateNotification());
            }

            return $response;
        } elseif ($this->isWakeUpStateAnswer($request)) {
            $response = $this->storeConversationValue(self::WAKE_UP_STATE, WakeUpStateEnum::class, $request, $user, $conversationId);

            if ($this->haventAsked(self::SLEEP_QUALITY, $conversationId)) {
                $user->notifyNow(new AskUserSleepQualityNotification());
            }

            return $response;
        } elseif ($this->isSleepQualityAnswer($request)) {
            $this->storeConversationValue(self::SLEEP_QUALITY, SleepQualityEnum::class, $request, $user, $conversationId);
        }

        return response()->json(['status' => '@todo']);
    }

    private function storeConversationValue(string $key, string $enum, Request $request, User $user, string $conversationId): JsonResponse
    {
        $cacheKey = sprintf('%s-%s', $key, $conversationId);

        $mood = $enum::fromString(Arr::get($request->all(), 'callback_query.data'));

        if ($mood === null) {
            return response()->json(['status' => 'invalid-mood']);
        }

        $prevMods = Cache::get($cacheKey);

        if ($prevMods) {
            $mods = json_decode($prevMods, true);
            $mods[] = $mood->value;
        } else {
            $mods = [$mood->value];
        }

        Cache::put(
            key: $cacheKey,
            value: json_encode(array_unique($mods)),
            ttl: Carbon::now()->addHours(12)
        );

        return response()->json(['status' => $key]);
    }

    private function haventAsked(string $key, string $conversationId): bool
    {
        return Cache::missing(sprintf('%s-%s', $key, $conversationId));
    }

    private function tryToAssignTelegramIdToUser(string $telegramUserId): JsonResponse
    {
        $code = trim(Arr::get(explode('/start', $message ?? ''), 1, ''));

        if (strlen($code) === 0) {
            return response()->json(['status' => 'no-code']);
        }

        try {
            $decryptedCode = decrypt($code);

            if (preg_match('/^tracker:(\d+)$/', $decryptedCode, $matches)) {
                $userId = $matches[1];

                $user = User::find($userId);

                if ($user !== null && $user->telegram_user_id !== $telegramUserId) {
                    $user->telegram_user_id = $telegramUserId;
                    $user->save();

                    $user->notifyNow(new TelegramReadyNotification());

                    return response()->json(['status' => 'telegram-id-assigned']);
                }

                return response()->json(['status' => 'invalid-user']);
            }

            return response()->json(['status' => 'invalid-code-format']);
        } catch (DecryptException $e) {
            return response()->json(['status' => 'invalid-code']);
        }
    }

    private function isMoodAnswer(Request $request): bool
    {
        return Arr::get($request->all(), 'callback_query.message.text') === AskUserMoodNotification::QUESTION;
    }

    private function isSleepQualityAnswer(Request $request): bool
    {
        return Arr::get($request->all(), 'callback_query.message.text') === AskUserSleepQualityNotification::QUESTION;
    }

    private function isWakeUpStateAnswer(Request $request): bool
    {
        return Arr::get($request->all(), 'callback_query.message.text') === AskUserWakeUpStateNotification::QUESTION;

    }
}
