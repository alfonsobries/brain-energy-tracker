<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'telegram_user_id' => $telegramUserId,
        ] = config('site.admin');

        User::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'telegram_user_id' => $telegramUserId,
        ]);
    }
}
