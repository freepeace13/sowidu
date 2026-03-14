<?php

namespace App\Events\Order;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Support\Models\InteractsWithModelChanges;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithModelChanges, InteractsWithSockets, SerializesModels;

    public array $changes;

    public function __construct(
        public Order $order,
        public User $causer,
        public ?Company $company = null,
    ) {
        $this->dontBroadcastToCurrentUser();

        $this->changes = $this->getModelColumnChanges($order);
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
        return 'order.updated';
    }
}
