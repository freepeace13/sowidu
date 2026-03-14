<?php

namespace App\Support;

use Account;
use Closure;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PushedResourceEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \Illuminate\Contracts\Auth\Access\Authorizable
     */
    public $subscriber;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Authorizable $subscriber)
    {
        $this->subscriber = $subscriber->fresh();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(model_morphs_stringify(
            Account::group($this->subscriber),
        ));
    }

    /**
     * @return mixed
     */
    public function impersonate(Closure $callback)
    {
        return Account::actingAs($this->subscriber, $callback);
    }
}
