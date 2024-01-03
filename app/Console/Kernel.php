<?php

namespace App\Console;

use App\Console\Commands\Ask;
use App\Console\Commands\FinishConversation;
use App\Console\Commands\RememberMissingData;
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
        collect(QuestionsEnum::cases())->each(fn (QuestionsEnum $question) => $schedule->command(Ask::class, [
            'question' => $question->value,
        ])->dailyAt($question->time()));

        $schedule->command(FinishConversation::class)->dailyAt('23:00');

        $schedule->command(RememberMissingData::class)->dailyAt('07:00');
        $schedule->command(RememberMissingData::class)->dailyAt('11:12');
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
