<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GetUserTelegramCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:code {--user-id= : User ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the Telegram Instruction for the user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->option('user-id');

        $user = $userId === null ? User::whereEmail(config('site.admin.email'))->first() : User::findOrFail($userId);

        $this->line('Telegram command is:'."\n");

        $this->info($user->telegramCommand());

        return Command::SUCCESS;
    }
}
