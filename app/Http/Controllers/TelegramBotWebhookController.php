<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\TelegramReadyNotification;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TelegramBotWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $message = Arr::get($request->all(), 'message.text');

        $telegramUserId = Arr::get($request->all(), 'message.from.id');

        $code = trim(Arr::get(explode('/start', $message ?? ''), 1, ''));

        if (strlen($code) === 0 || $telegramUserId === null) {
            return response()->json(['success' => true]);
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
                }
            }
        } catch (DecryptException $e) {
            // nothing to do...
        }

        return response()->json(['success' => true]);
    }
}
