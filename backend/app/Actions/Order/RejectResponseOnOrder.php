<?php

namespace App\Actions\Order;

use App\Events\Order\OrderCancelled;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Gate;

/**
 * Order was rejected by the user. Order wants to be `Cancelled`
 */
class RejectResponseOnOrder
{
    public function reject(User $user, Order $order, ?Company $company = null): Order
    {
        Gate::forUser($user)->authorize('cancelOrReject', $order);

        $service = OrderService::make($user, $company);

        // Verify that user can reject this order
        abort_unless(
            $service->isRequiresResponse($order) || $service->isCurrentlyOwned($order),
            'You are not allowed to make this action.',
        );

        $order = $service->rejectOrder($order);

        // Trigger event
        event(new OrderCancelled($order, $user, $company));

        return $order;
    }
}
