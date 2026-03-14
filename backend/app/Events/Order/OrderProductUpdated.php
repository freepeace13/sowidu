<?php

namespace App\Events\Order;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderProductUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $causer,
        public Order $order,
        public OrderProduct $orderProduct,
    ) {
        //
    }
}
