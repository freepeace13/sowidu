<?php

namespace App\Actions\Order\Incoming;

use App\Actions\Addressbook\Person\CreatesForeignPersonAddressbook;
use App\Actions\Traits\HasAddressFields;
use App\Events\Order\IncomingOrderCreated;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Sowidu\SharedData\SharedData;

class CreatesIncomingOrderForForeignClient
{
    use HasAddressFields;

    public function create(User $user, array $inputs, ?Company $team)
    {
        Gate::forUser($user)->authorize('create', Order::class);

        // Validate order inputs
        $validated = Validator::make($inputs, [
            'description' => 'required|string',
            'order_date' => 'required|date|before_or_equal:now',
            'planned_start_date' => 'nullable|date|after_or_equal:now',
            'planned_finish_date' => 'nullable|date|after_or_equal:now',
            'client' => [
                'required',
                'array',
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

        // Insert default avatar
        $clientInputs = Arr::set(
            $validated['client'],
            'photo',
            app(SharedData::class)->get('defaults.avatars.unset'),
        );

        // Create person address-book first
        $clientAddressbook = (new CreatesForeignPersonAddressbook)
            ->create($user, $clientInputs, $team?->id);

        $orderService = OrderService::make($user, $team);

        $client = $orderService->getClientFromAddressbook($clientAddressbook);

        // Save order inputs
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
