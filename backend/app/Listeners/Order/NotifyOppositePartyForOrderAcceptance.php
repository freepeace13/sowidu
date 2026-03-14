<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderAcceptedResponse;
use App\Models\Employee;
use App\Notifications\Order\OrderAcceptedResponseNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyOppositePartyForOrderAcceptance implements ShouldQueue
{
    public function handle(OrderAcceptedResponse $event)
    {
        $company = $event->company;
        $order = $event->order;
        $userCauser = $event->userCauser;

        $causer = $company ?? $userCauser;

        // Identify which party updated the `order` to whom will be notified
        $oppositeParty = $order->getOppositeParty($userCauser, $company);

        // TODO - send email to foreign instance
        if (morph_is($oppositeParty, Addressbook::class)) {
            return;
        }

        // Send notification to opposite party
        if (morph_is($oppositeParty, User::class)) {
            return Notification::send(
                $oppositeParty,
                new OrderAcceptedResponseNotification(
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
                    new OrderAcceptedResponseNotification(
                        $order,
                        $causer,
                    ),
                ));
        }
    }
}
