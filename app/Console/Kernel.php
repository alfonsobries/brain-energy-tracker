<?php

namespace App\Console;

use App\Enums\QuestionsEnum;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        collect(QuestionsEnum::cases())->each(fn (QuestionsEnum $question) => $schedule->command('telegram:ask', [
            'question' => $question->value,
        ])->dailyAt($question->time()));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
