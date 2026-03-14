<?php

namespace App\Events\Contact;

use App\Contracts\Auth\AuthorizableGroup;
use App\Contracts\Contactable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RelationshipUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \App\Contracts\Auth\AuthorizableGroup
     */
    public $subscriber;

    /**
     * @var array
     */
    public $contactable;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Authorizable  $subscriber
     * @return void
     */
    public function __construct(AuthorizableGroup $subscriber, Contactable $contactable)
    {
        $this->contactable = $contactable;
        $this->subscriber = $subscriber;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(model_morphs_stringify($this->subscriber));
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->contactable->getKey(),
            'alias' => $this->contactable->getMorphClass(),
        ];
    }
}
