<?php

namespace App\Listeners\Order\Log;

use App\Enums\OrderEvent;
use App\Events\Order\OrderCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogCancelledOrder implements ShouldQueue
{
    public function handle(OrderCancelled $event)
    {
        activity_log($event->order)
            ->orderLog()
            ->setCauser($event->userCauser)
            ->updateStatus(OrderEvent::CANCELLED);
    }
}
