<?php

namespace App\Console\Commands;

use App\Enums\QuestionsEnum;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Ask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:ask {question}';

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
        $question = QuestionsEnum::fromString($this->argument('question'));

        if ($question === null) {
            Log::info('Question not found', [
                'question' => $this->argument('question'),
            ]);

            $this->error('Question not found');

            return;
        }

        $users = User::all();

        $users->each(function (User $user) use ($question) {
            $notification = $question->notification();

            if ($notification->alreadyAsked($user->getConversationId())) {
                return;
            }

            $user->ask($question);
        });
    }
}
