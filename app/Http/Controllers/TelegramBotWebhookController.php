<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\QuestionsEnum;
use App\Models\User;
use App\Notifications\AskUserMoodNotification;
use App\Notifications\AskUserSleepQualityNotification;
use App\Notifications\AskUserWakeUpStateNotification;
use App\Notifications\TelegramReadyNotification;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TelegramBotWebhookController extends Controller
{
    // const SLEEP_QUALITY = 'sleep-quality';

    // const WAKE_UP_STATE = 'wake-up-state';

    // const MOOD = 'mood';

    // const QUESTIONS = [
    //     self::MOOD => AskUserMoodNotification::class,
    //     self::WAKE_UP_STATE => AskUserWakeUpStateNotification::class,
    //     self::SLEEP_QUALITY => AskUserSleepQualityNotification::class,
    // ];

    private function getUser(string|int $telegramUserId): ?User
    {
        return User::where('telegram_user_id', $telegramUserId)->first();
    }

    private function getTelegramUserId(Request $request): string|int|null
    {
        return Arr::get($request->all(), 'message.from.id', Arr::get($request->all(), 'callback_query.from.id'));
    }

    public function __invoke(Request $request): JsonResponse
    {
        $telegramUserId = $this->getTelegramUserId($request);

        if ($telegramUserId === null) {
            Log::info('No telegram user ID');

            return response()->json(['status' => 'no-telegram-user-id']);
        }

        $user = $this->getUser($telegramUserId);

        if ($user === null) {
            Log::info('User not found', ['telegram_user_id' => $telegramUserId]);

            return $this->tryToAssignTelegramIdToUser($telegramUserId);
        }

        $conversationId = $user->getConversationId();

        if ($conversationId === null) {
            Log::info('No conversation ID', ['telegram_user_id' => $telegramUserId]);

            return response()->json(['status' => 'no-conversation-id']);
        }

        $messageText = Arr::get($request->all(), 'callback_query.message.text', '');

        $question = QuestionsEnum::fromQuestion($messageText);

        if ($question === null) {
            Log::info('No question found', ['telegram_user_id' => $telegramUserId, 'message_text' => $messageText]);

            return response()->json(['status' => 'no-question']);
        }

        try {
            $question->storeAnswerInCache(
                conversationId: $conversationId,
                answerText: Arr::get($request->all(), 'callback_query.data', '')
            );

            $question = $question->nextQuestion();

            if ($question !== null && $question->notAskedYet($conversationId)) {
                $user->notifyNow($question);

                return response()->json(['status' => 'next-question']);
            }
        } catch (\Exception $e) {
            Log::info('Invalid answer', ['telegram_user_id' => $telegramUserId, 'message_text' => $messageText]);

            return response()->json(['status' => 'invalid-answer']);
        }

        return response()->json(['status' => '@todo']);
    }

    private function haventAsked(string $key, string $conversationId): bool
    {
        return Cache::missing(sprintf('%s-%s', $key, $conversationId));
    }

    private function tryToAssignTelegramIdToUser(string|int $telegramUserId): JsonResponse
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
}
