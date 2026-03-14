<?php

namespace App\Events\Addressbook;

use App\Models\Addressbook;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddressbookDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Addressbook $addressbook)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
