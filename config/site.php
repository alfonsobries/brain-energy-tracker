<?php

declare(strict_types=1);

return [
    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin'),
        'email' => env('ADMIN_EMAIL', 'foo@bar.com'),
        'password' => env('ADMIN_PASSWORD', 'password'),
        'telegram_user_id' => env('ADMIN_TELEGRAM_USER_ID', null),
    ],
];
