<?php

namespace App\Events\Todo\Subscriber;

use App\Models\Board;
use App\Models\Subscriber;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardSubscriberAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Subscriber $subscriber,
        public Board $board,
    ) {}
}
