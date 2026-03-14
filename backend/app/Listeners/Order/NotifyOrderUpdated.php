<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderUpdated;
use App\Models\Addressbook;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Order;
use App\Notifications\Order\OrderPrimaryDetailsUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyOrderUpdated implements ShouldQueue
{
    /**
     * Handle
     */
    public function handle(OrderUpdated $event)
    {
        $company = $event->company;
        $order = $event->order;
        $userCauser = $event->causer;
        $columnChanges = array_keys($event->changes);

        $causer = $company ?? $userCauser;

        // Identify `Order` column changed are valid
        if (!collect($columnChanges)->contains(fn ($value) => in_array($value, [
            'description',
            'status',
            'order_date',
            'planned_start_date',
            'planned_finish_date',
        ]))) {
            return;
        }

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
                new OrderPrimaryDetailsUpdatedNotification(
                    $order,
                    $causer,
                    $columnChanges,
                ),
            );
        }

        if (morph_is($oppositeParty, Company::class)) {
            // Notify company employees
            return $oppositeParty->employees()
                ->get()
                ->each(fn (Employee $employee) => Notification::send(
                    $employee,
                    new OrderPrimaryDetailsUpdatedNotification(
                        $order,
                        $causer,
                        $columnChanges,
                    ),
                ));
        }
    }
}
