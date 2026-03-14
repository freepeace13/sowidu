<?php

namespace App\Listeners\Order\Log;

use App\Enums\OrderEvent;
use App\Enums\OrderStatus;
use App\Events\Order\OrderAcceptedResponse;
use App\Models\Order;
use App\Models\User;

class LogOrderEvents
{
    public function handle(OrderAcceptedResponse $event)
    {
        $order = $event->order;
        $orderEvent = null;

        switch ($order->status) {
            case OrderStatus::COMMISSIONED:
                $orderEvent = OrderEvent::CONFIRMED;
                break;
            case OrderStatus::READY_FOR_REVIEW:
                $orderEvent = OrderEvent::FINISH_WORKING;
                break;
            case OrderStatus::REJECT:
                $orderEvent = OrderEvent::CLIENT_REJECTED;
                break;
            case OrderStatus::WORK_ON_REVISIONS:
                $orderEvent = OrderEvent::START_WORKING;
                break;
            case OrderStatus::FINISHED:
                $orderEvent = OrderEvent::CLIENT_ACCEPTED;
                break;
        }

        if (!$orderEvent) {
            return;
        }

        activity_log($event->order)
            ->orderLog()
            ->setCauser($event->userCauser)
            ->updateStatus($orderEvent);

        if ($orderEvent === OrderEvent::CLIENT_ACCEPTED) {
            $this->clientAcceptedAfterReviewing($event->order, $event->userCauser);
        }
    }

    public function clientAcceptedAfterReviewing(Order $order, User $userCauser)
    {
        // Client accepted - make this order `completed`
        activity_log($order)
            ->orderLog()
            ->setCauser($userCauser)
            ->updateStatus(OrderEvent::COMPLETED);
    }
}
