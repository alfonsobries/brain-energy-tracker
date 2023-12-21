<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use NotificationChannels\Telegram\Telegram;

class ConfigureTelegramWebhoook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhoook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the Telegram webhook for the bot';

    public function handle(): int
    {
        $telegram = app(Telegram::class);

        $apiUri = sprintf('%s/bot%s/%s', $telegram->getApiBaseUri(), $telegram->getToken(), 'setWebhook');

        Http::post($apiUri, [
            'url' => route('telegram.webhook'),
        ]);

        $this->info(sprintf('Telegram webhook configured to %s !', route('telegram.webhook')));

        return Command::SUCCESS;
    }
}
