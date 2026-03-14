<?php

namespace Modules\Todos\Events\Subscriber;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Subscriber;

class BoardSubscriberAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Subscriber $subscriber,
        public Board $board,
    ) {}
}
