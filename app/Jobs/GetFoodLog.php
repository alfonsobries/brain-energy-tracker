<?php

namespace App\Jobs;

use App\Models\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log as LogFacade;

class GetFoodLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Log $log, private string $foodDescription)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = Http::ingredients($this->foodDescription);

        LogFacade::info(is_string($data) ? 'String' : 'Not string');

        try {
            $data = json_decode($data, true);

            LogFacade::info(is_array($data) ? 'Array' : 'Not array');

            try {
                foreach ($data as $value) {
                    LogFacade::info(is_array($value) ? 'Array caue' : 'Not array value');

                    LogFacade::info($value);

                    if (is_array($value)) {
                        $this->log->food()->create($value);
                    }
                }

            } catch (\Exception $e) {
                LogFacade::info('Cannot store. Message: '.$e->getMessage());

                LogFacade::info($data);
            }

        } catch (\Exception $e) {
            LogFacade::info('Cannot parse. Message: '.$e->getMessage());

            LogFacade::info($data);
        }

    }
}
