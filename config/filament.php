<?php

return [
    'avatar' => [
        'provider' => \App\Models\User::class,
    ],

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    'locales' => [
        'ru' => 'Русский',
    ],

    'default_locale' => 'ru',
];
