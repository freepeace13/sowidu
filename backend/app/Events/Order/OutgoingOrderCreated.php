<?php

namespace App\Events\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OutgoingOrderCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order, public User $author)
    {
        //
    }
}
