<?php

namespace App\Actions\Order;

use App\Enums\OrderStatus;
use App\Events\Order\OrderRejectedToFinished;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Gate;

class DisapproveResponseOnOrder
{
    public function disapprove(User $user, Order $order, ?Company $company = null): Order
    {
        Gate::forUser($user)->authorize('cancelOrReject', $order);

        $service = OrderService::make($user, $company);

        // Verify that user can reject this order
        abort_unless(
            $service->getNeededResponseValue($order) === OrderStatus::FINISHED,
            'You are not allowed to make this action.',
        );

        $order = $service->disapproveReview($order);

        // Trigger event
        event(new OrderRejectedToFinished($order, $user, $company));

        return $order;
    }
}
