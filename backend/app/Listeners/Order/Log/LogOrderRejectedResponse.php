<?php

namespace App\Listeners\Order\Log;

use App\Enums\OrderEvent;
use App\Events\Order\OrderRejectedToFinished;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogOrderRejectedResponse implements ShouldQueue
{
    public function handle(OrderRejectedToFinished $event)
    {
        activity_log($event->order)
            ->orderLog()
            ->setCauser($event->userCauser)
            ->updateStatus(OrderEvent::CLIENT_REJECTED);
    }
}
