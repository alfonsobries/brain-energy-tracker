<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CommandEnum;
use App\Enums\QuestionsEnum;
use App\Models\User;
use App\Notifications\AllQuestionsAnswered;
use App\Notifications\MissingAnswers;
use App\Notifications\NoActiveConversation;
use App\Notifications\TelegramReadyNotification;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class TelegramBotWebhookController extends Controller
{
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

        $text = Arr::get($request->all(), 'message.text', '');

        $command = CommandEnum::fromString($text);

        if ($command !== null) {
            return $this->handleCommand($user, $command);
        }

        if ($conversationId === null) {
            $user->notify(new NoActiveConversation());

            return response()->json(['status' => 'no-conversation-id']);
        }

        $question = Arr::get($request->all(), 'callback_query.message.text', '');

        $question = QuestionsEnum::fromQuestion($question);

        $answerText = Arr::get($request->all(), 'callback_query.data', $text);

        if ($question === null) {
            $question = QuestionsEnum::lastAsked($conversationId);
        }

        if ($question === null) {
            Log::info('No question found', ['telegram_user_id' => $telegramUserId, 'question' => $question]);

            return response()->json(['status' => 'no-question']);
        }

        try {
            $question->storeAnswerInCache(
                conversationId: $conversationId,
                answerText: $answerText
            );

            if ($user->allQuestionsAnswered()) {
                $user->notify(new AllQuestionsAnswered());

            }
        } catch (\Exception $e) {
            Log::info('Invalid answer', ['telegram_user_id' => $telegramUserId, 'question' => $question, 'answer' => $answerText]);

            return response()->json(['status' => 'invalid-answer']);
        }

        // $question = $question->nextQuestion();

        // if ($question === null) {
        //     return response()->json(['status' => 'no-next-question']);
        // }

        // if ($question->notification()->notAskedYet($conversationId)) {
        //     $user->ask($question);
        // }

        // return response()->json(['status' => 'next-question']);

        return response()->json(['status' => 'answer-stored']);
    }

    private function handleCommand(User $user, CommandEnum $command): JsonResponse
    {
        match ($command) {
            CommandEnum::GET_MISSING => $user->notify(new MissingAnswers()),
            CommandEnum::RESTART_SESSION => $user->restartConversation(),
            CommandEnum::FINISH_SESSION => $user->finishConversation(),
            CommandEnum::BREAKFAST => $user->ask(QuestionsEnum::BREAKFAST),
            CommandEnum::DINNER => $user->ask(QuestionsEnum::DINNER),
            CommandEnum::LUNCH => $user->ask(QuestionsEnum::LUNCH),
            CommandEnum::DINNER => $user->ask(QuestionsEnum::DINNER),
            CommandEnum::SNACK => $user->ask(QuestionsEnum::SNACK),
            CommandEnum::MOOD => $user->ask(QuestionsEnum::MOOD),
            CommandEnum::WATER => $user->ask(QuestionsEnum::WATER),
            CommandEnum::SLEEP_QUALITY => $user->ask(QuestionsEnum::SLEEP_QUALITY),
            CommandEnum::WAKE_UP_STATE => $user->ask(QuestionsEnum::WAKE_UP_STATE),
            CommandEnum::SYMPTOMS => $user->ask(QuestionsEnum::SYMPTOMS),
        };

        return response()->json(['status' => 'command-handled']);
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
