<?php

use App\Http\Controllers\Inertia\Reminders\Handlers;

return [
    'enabled' => env('REMINDERS_ENABLED', false),

    'handlers' => [
        'setup-account-address' => Handlers\UpdateAccountAddress::class,
    ],
];
