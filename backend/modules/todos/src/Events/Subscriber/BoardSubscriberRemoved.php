<?php

namespace Modules\Todos\Events\Subscriber;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Subscriber;

class BoardSubscriberRemoved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public Subscriber $subscriber,
        public Board $board,
    ) {}
}
