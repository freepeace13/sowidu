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

class EmployeeStartedWorkingOnOrder implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order, public User $causer)
    {
        $this->dontBroadcastToCurrentUser();
    }

    public function broadCastOn()
    {
        return new PrivateChannel("orders.{$this->order->id}");
    }

    public function broadcastAs()
    {
        return 'order.employee.started.working';
    }
}
