<?php

namespace App\Services\DeliveryTicket;

use App\Http\Middleware\HandleInertiaRequests;
use Modules\DeliveryTicket\Contracts\External\InertiaContract;

class InertiaAdapter implements InertiaContract
{
    public function __construct(
        protected HandleInertiaRequests $middleware,
    ) {}

    public function getSharedData(): array
    {
        return [];
    }

    public function getRootView(): string
    {
        return 'delivery-ticket::app';
    }
}
