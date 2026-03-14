<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderRejectedToFinished;
use App\Models\Addressbook;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\Order\OrderRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyContractorForRejectedOrder implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(OrderRejectedToFinished $event)
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
                new OrderRejectedNotification(
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
                    new OrderRejectedNotification(
                        $order,
                        $causer,
                    ),
                ));
        }
    }
}
