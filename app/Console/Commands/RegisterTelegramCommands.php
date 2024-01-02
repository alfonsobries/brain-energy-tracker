<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\CommandEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use NotificationChannels\Telegram\Telegram;

class RegisterTelegramCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:register-commands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register the Telegram commands';

    public function handle(): int
    {
        $telegram = app(Telegram::class);

        $apiUri = sprintf('%s/bot%s/%s', $telegram->getApiBaseUri(), $telegram->getToken(), 'setMyCommands');

        $commands = array_map(fn ($command) => [
            'command' => $command->value,
            'description' => $command->description(),
        ], CommandEnum::cases());

        Http::post($apiUri, [
            'commands' => $commands,
        ]);

        $this->info('Telegram commands registered');

        return Command::SUCCESS;
    }
}
