<?php

use App\Enums\Permissions;
use Modules\DeliveryTicket\Http\Middleware\DeliveryTicketsInertiaHandler;
use Modules\DeliveryTicket\Models\DeliveryTicket;

return [
    'domain' => '',

    'prefix' => 'delivery-tickets',

    'middleware' => [
        'web',
        'auth',
        'impersonating',
        DeliveryTicketsInertiaHandler::class,
        'permission:' . Permissions::CAN_ACCESS_DELIVERY_TICKETS,
    ],

    'json_middleware' => [
        'web',
        'auth',
        'json',
        'permission:' . Permissions::CAN_ACCESS_DELIVERY_TICKETS,
    ],

    'permissions' => [
        'can_access_delivery_tickets' => Permissions::CAN_ACCESS_DELIVERY_TICKETS,
    ],

    'models' => [
        'delivery_tickets' => DeliveryTicket::class,
    ],
];
