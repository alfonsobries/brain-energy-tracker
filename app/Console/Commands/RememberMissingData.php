<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\MissingAnswers;
use Illuminate\Console\Command;

class RememberMissingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remember-missing-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remember missing data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();

        $users
            ->filter(fn (User $user) => $user->allQuestionsAnswered())
            ->each(fn (User $user) => $user->notify(new MissingAnswers()));
    }
}
