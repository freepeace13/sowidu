<?php

namespace App\Actions\Order\Incoming;

use App\Actions\Traits\HasAddressFields;
use App\Events\Order\OrderUpdated;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Validator;

class UpdateIncomingOrder
{
    use HasAddressFields;

    public function update(User $user, Order $order, array $inputs, ?Company $team = null)
    {
        $validated = Validator::make($inputs, [
            'description' => 'required|string',
            'order_date' => 'required|date|before_or_equal:now',
            'planned_start_date' => 'nullable|date',
            'planned_finish_date' => 'nullable|date',
            'client_id' => 'nullable',
        ])->validate();

        $orderService = OrderService::make($user, $team);

        if (array_key_exists('client_id', $validated)) {
            // Update Client
            $orderService->updateClient($order, $validated['client_id']);
        }

        $orderService->updateOrderPrimaryDetails($order, $validated);

        event(new OrderUpdated($order, $user, $team));

        return $order;
    }
}
