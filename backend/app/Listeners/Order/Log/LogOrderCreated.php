<?php

namespace App\Listeners\Order\Log;

use App\Events\Order\IncomingOrderCreated;
use App\Events\Order\OutgoingOrderCreated;
use App\Repositories\ActivityLog\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogOrderCreated implements ShouldQueue
{
    public function handle(OutgoingOrderCreated|IncomingOrderCreated $event)
    {
        (new ActivityLog($event->order, $event->author))
            ->orderLog()
            ->setCauser($event->author)
            ->created();
    }
}
