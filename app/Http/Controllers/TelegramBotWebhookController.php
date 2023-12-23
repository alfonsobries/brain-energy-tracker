<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MoodEnum;
use App\Models\User;
use App\Notifications\AskUserMoodNotification;
use App\Notifications\TelegramReadyNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class TelegramBotWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $telegramUserId = Arr::get($request->all(), 'message.from.id', Arr::get($request->all(), 'callback_query.from.id'));

        if ($telegramUserId === null) {
            return response()->json(['status' => 'no-telegram-user-id']);
        }

        $user = User::where('telegram_user_id', $telegramUserId)->first();

        if ($user === null) {
            return $this->tryToAssignTelegramIdToUser($telegramUserId);
        }
        $conversationId = $user->getConversationId();

        if ($conversationId === null) {
            return null;
        }

        if ($this->isMoodAnswer($request)) {
            $mood = MoodEnum::fromString(Arr::get($request->all(), 'callback_query.data'));

            if ($mood === null) {
                return response()->json(['status' => 'invalid-mood']);
            }

            Cache::put(
                key: sprintf('mood-%s', $conversationId),
                value: $mood->value,
                ttl: Carbon::now()->addHours(12)
            );
        }

        return response()->json(['status' => '@todo']);
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
}
