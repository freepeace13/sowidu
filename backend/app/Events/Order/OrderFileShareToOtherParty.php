<?php

namespace App\Events\Order;

use App\Models\Addressbook;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Order;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class OrderFileShareToOtherParty implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Order $order,
        public Media $mediaFile,
        public User|Employee $account,
        public User $author,
        public ?Company $company,
        public User|Company|Addressbook $oppositeParty,
    ) {
        $this->dontBroadcastToCurrentUser();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('orders.' . $this->order->id);
    }
}
