<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderCancelled;
use App\Models\Employee;
use App\Notifications\Order\OrderCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyOppositePartyForCancelledOrder implements ShouldQueue
{
    public function handle(OrderCancelled $event)
    {
        $company = $event->company;
        $order = $event->order;
        $userCauser = $event->userCauser;

        $causer = $company ?? $userCauser;

        // Identify which party updated the `order`
        $oppositeParty = $order->getOppositeParty($userCauser, $company);

        // TODO - send email to foreign instance
        if (morph_is($oppositeParty, Addressbook::class)) {
            return;
        }

        // Send notification to opposite party
        if (morph_is($oppositeParty, User::class)) {
            return Notification::send(
                $oppositeParty,
                new OrderCancelledNotification(
                    $order,
                    $causer,
                ),
            );
        }

        if (morph_is($oppositeParty, Company::class)) {
            // Notify company employees
            return $oppositeParty->employees()
                ->get()
                ->each(fn (Employee $employee) => Notification::send(
                    $employee,
                    new OrderCancelledNotification(
                        $order,
                        $causer,
                    ),
                ));
        }
    }
}
