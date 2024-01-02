<?php

namespace App\Jobs;

use App\Models\Log;
use App\Notifications\ErrorNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

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
        $user = $this->log->user;

        try {
            $response = Http::ingredients($this->foodDescription);
        } catch (\Exception $e) {
            $user->notify(new ErrorNotification('Cannot get food from API: '.$e->getMessage().' '.$this->foodDescription));

            return;
        }

        if (! is_string($response)) {
            $user->notify(new ErrorNotification('Data returned from API is not a string'));

            return;
        }

        try {
            $data = json_decode($response, true);

            if (! is_array($data)) {
                throw new \Exception('Data parsed from the json string is not an array');

                return;
            }

            foreach ($data as $value) {
                try {
                    $this->log->food()->create($value);
                } catch (\Exception $e) {
                    $user->notify(new ErrorNotification('*Cannot store:* '."\n\n".'Message: '.$e->getMessage()."\n\n".'Response: '.$response));
                }
            }

        } catch (\Exception $e) {
            $user->notify(new ErrorNotification('*Cannot parse api response:* '."\n\n".'Message: '.$e->getMessage()."\n\n".'Response: '.$response));
        }
    }
}
