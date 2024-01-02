<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FinishConversation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:finish-conversation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finish the active conversation and store the data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();

        $users
            ->filter(fn (User $user) => $user->allQuestionsAnswered())
            ->each(fn (User $user) => $user->finishConversation());
    }
}
