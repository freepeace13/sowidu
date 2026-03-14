<?php

use App\Http\Middleware\Web\CatalogHandleInertiaRequests;
use Modules\Shared\Enums\Permissions;

return [
    'prefix' => 'catalog',
    'middleware' => [
        'web',
        CatalogHandleInertiaRequests::class,
        'permission:' . Permissions::CAN_ACCESS_CATALOG,
    ],
];
