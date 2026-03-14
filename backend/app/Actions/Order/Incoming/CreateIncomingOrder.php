<?php

namespace App\Actions\Order\Incoming;

use App\Actions\Traits\HasAddressFields;
use App\Events\Order\IncomingOrderCreated;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class CreateIncomingOrder
{
    use HasAddressFields;

    public function create(User $user, array $inputs, ?Company $team = null)
    {
        Gate::forUser($user)->authorize('create', Order::class);

        $validated = Validator::make($inputs, [
            'description' => 'required|string',
            'order_date' => 'required|date',
            'planned_start_date' => 'nullable|date',
            'planned_finish_date' => 'nullable|date',
            'client_id' => [
                'required',
                'integer',
            ],
            'delivery_address' => [
                'required',
                'array',
            ],
            'delivery_address.country' => [
                function ($attribute, $value, $fail) use ($inputs) {
                    if (!Arr::has($inputs, 'id')) {
                        $fail("Delivery $attribute is required.");
                    }
                },
            ],
            'delivery_address.state' => [
                function ($attribute, $value, $fail) use ($inputs) {
                    if (!Arr::has($inputs, 'id')) {
                        $fail("Delivery $attribute is required.");
                    }
                },
            ],
            'delivery_address.city' => [
                function ($attribute, $value, $fail) use ($inputs) {
                    if (!Arr::has($inputs, 'id')) {
                        $fail("Delivery $attribute is required.");
                    }
                },
            ],
            'delivery_address.id' => [
                'sometimes',
                'integer',
                'exists:places,id',
            ],
        ])->validate();

        $orderService = OrderService::make($user, $team);

        $clientAddressbook = $orderService->getAddressbook($validated['client_id']);

        $client = $orderService->getClientFromAddressbook($clientAddressbook);

        $order = $orderService->newIncomingOrder($client, $clientAddressbook, $validated);

        // Add delivery address
        $order->addOrUpdateDeliveryAddress(
            $order->order_number,
            $this->parseCountryInput($validated['delivery_address']),
        );

        event(new IncomingOrderCreated($order, $user));

        return $order;
    }
}
