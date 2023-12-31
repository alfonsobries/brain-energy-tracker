<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class StartConversation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:start-conversation {--user-id= : User ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a conversation with the user by asking them how they feel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');

        $user = $userId === null ? User::whereEmail(config('site.admin.email'))->first() : User::findOrFail($userId);

        $user->startConversation();
    }
}
