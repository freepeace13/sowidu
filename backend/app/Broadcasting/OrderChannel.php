<?php

namespace App\Broadcasting;

use App\Models\Order;
use App\Services\Order\OrderService;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Contracts\Auth\Access\Authorizable;

class OrderChannel
{
    use InteractsWithImpersonator;

    /**
     * Authenticate the user's access to the channel.
     *
     *
     * @return array|bool
     */
    public function join(Authorizable $user, Order $order)
    {
        return OrderService::make($user, $this->getCurrentCompany())->isInvolvedOnOrder($order);
    }
}
