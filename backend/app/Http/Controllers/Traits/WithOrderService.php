<?php

namespace App\Http\Controllers\Traits;

use App\Services\Order\OrderService;
use App\Traits\InteractsWithImpersonator;

trait WithOrderService
{
    use InteractsWithImpersonator;

    /** @return OrderService|\App\Models\Order */
    protected function createOrderService(?int $userId = null, ?int $teamId = null): OrderService
    {
        return OrderService::make(
            $userId ?? $this->getCurrentUser(),
            $teamId ?? $this->getCurrentTeam(),
        );
    }
}
