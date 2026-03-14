<?php

namespace App\Events\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncomingOrderCreated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order, public User $author)
    {
        $this->dontBroadcastToCurrentUser();
    }

    public function broadCastOn()
    {
        return new PrivateChannel("company.{$this->order->team_id}.orders");
    }

    public function broadcastAs()
    {
        return 'company.new.order';
    }
}
