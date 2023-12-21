<?php

use App\Http\Controllers\TelegramBotWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('telegram/webhook', TelegramBotWebhookController::class)->name('telegram.webhook');
