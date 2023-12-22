<?php

namespace App\Providers;

use App\Enums\SymptomEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $instructions = file_get_contents(resource_path('get_meals_instructions.md'));

        Http::macro('ingredients', fn ($foodDescription) => Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.config('services.openai.key'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [[
                'role' => 'user',
                'content' => sprintf($instructions, $foodDescription),
            ]],
        ])->json('choices.0.message.content'));

        $logInstructions = file_get_contents(resource_path('get_log_instructions.md'));

        Http::macro('symptons', fn ($description) => Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.config('services.openai.key'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [[
                'role' => 'user',
                'content' => sprintf($logInstructions, SymptomEnum::collect()->join(', '), $description),
            ]],
        ])->json('choices.0.message.content'));
    }
}
