<?php

namespace App\Actions\Order\Outgoing;

use App\Actions\Traits\HasAddressFields;
use App\Events\Order\OutgoingOrderCreated;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\AddressbookService;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Validator;

class CreateOutgoingOrder
{
    use HasAddressFields;

    public function create(User $user, array $inputs, ?Company $team = null)
    {
        $validated = Validator::make($inputs, [
            'description' => 'required|string',
            'order_date' => 'required|date',
            'planned_start_date' => 'nullable|date',
            'planned_finish_date' => 'nullable|date',
            'contractor_id' => [
                'required',
                'integer',
            ],
            'delivery_address' => [
                'required',
                'array',
            ],
            'delivery_address.id' => [
                'integer',
                'exists:places,id',
            ],
        ])->validate();

        // Parse `Contractor` from Addressbook
        $addressbookService = AddressbookService::make($user, $team);
        $contractorAddressbook = $addressbookService
            ->where('id', $validated['contractor_id'])
            ->firstOrFail();

        throw_validation_unless(
            $addressbookService->isOwned($contractorAddressbook),
            'You have no contact with this person, please refresh the page and try again.',
        );

        $orderService = OrderService::make($user, $team);

        $contractor = $orderService->getContractorFromAddressbook($contractorAddressbook);

        $order = $orderService
            ->newOutgoingOrder($contractor, $contractorAddressbook, $validated);

        // Add delivery address to outgoing order
        $order->addOrUpdateDeliveryAddress(
            $order->order_number,
            $validated['delivery_address'],
        );

        // Dispatch event
        event(new OutgoingOrderCreated($order, $user));

        return $order;
    }
}
