<?php

namespace App\Events\Order;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public User $userCauser,
        public ?Company $company,
    ) {
        $this->dontBroadcastToCurrentUser();
    }

    public function broadCastOn()
    {
        return [
            new PrivateChannel("orders.{$this->order->id}"),
            new PrivateChannel("company.{$this->order->team_id}.orders"),
        ];
    }

    public function broadcastAs()
    {
        return 'order.cancelled';
    }
}
