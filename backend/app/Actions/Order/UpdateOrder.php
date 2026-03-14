<?php

namespace App\Actions\Order;

use App\Actions\Traits\AsAction;
use App\Actions\Traits\HasAddressFields;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateOrder
{
    use AsAction;
    use HasAddressFields;

    public function handle(User $user, Company $company, Order $order, array $inputs)
    {
        Gate::forUser($user)->authorize('update', $order);
        $validated = $this->validate($inputs);

        if (Arr::has($validated, 'delivery_address')) {
            // Update delivery address
            $order->addOrUpdateDeliveryAddress(
                $order->order_number,
                $this->parseCountryInput($validated['delivery_address']),
            );
        }

        if (Arr::has($validated, 'planned_start_date')) {
            $order->update(['planned_start_date' => $validated['planned_start_date']]);
        }

        if (Arr::has($validated, 'planned_finish_date')) {
            $order->update(['planned_finish_date' => $validated['planned_finish_date']]);
        }

        if (Arr::has($validated, 'description')) {
            $order->update(['description' => $validated['description']]);
        }

        return $order;
    }

    public function validate(array $inputs)
    {
        $deliveryAddressValidation = [];
        if (Arr::has($inputs, 'delivery_address')) {
            $deliveryAddressValidation = [
                'delivery_address' => [
                    'required',
                    'array',
                ],
                'delivery_address.country' => [
                    'required_with:delivery_address',
                ],
                'delivery_address.state' => [
                    'required_with:delivery_address',
                ],
                'delivery_address.city' => [
                    'required_with:delivery_address',
                ],
                'delivery_address.id' => [
                    'sometimes',
                    'integer',
                    'exists:places,id',
                ],
            ];
        }

        return Validator::make(
            $inputs,
            array_merge(
                [
                    'planned_start_date' => 'nullable|date',
                    'planned_finish_date' => 'nullable|date',
                    'description' => 'nullable|string',
                ],
                $deliveryAddressValidation,
            ),
        )->validate();
    }
}
