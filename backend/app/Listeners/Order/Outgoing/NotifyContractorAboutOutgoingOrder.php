<?php

namespace App\Listeners\Order\Outgoing;

use App\Events\Order\OutgoingOrderCreated;
use App\Models\Company;
use App\Models\Employee;
use App\Notifications\Order\Contractor\NewIncomingOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyContractorAboutOutgoingOrder implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(OutgoingOrderCreated $event)
    {
        $order = $event->order;

        // Check if contractor is registered
        $contractor = $order->contractor;

        // Ignore client that are not registered
        if (!morph_is($contractor, Company::class)) {
            return;
        }

        // Notify contractor about the new order - get all contractor employees
        $contractor->employees()
            ->get()
            ->each(fn (Employee $employee) => Notification::send(
                $employee,
                new NewIncomingOrderNotification($order, $order->userAuthor),
            ));
    }
}
