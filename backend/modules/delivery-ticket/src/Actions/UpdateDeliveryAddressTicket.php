<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Actions;

use Illuminate\Support\Facades\Validator;
use Modules\DeliveryTicket\Contracts\Actions\UpdateDeliveryAddressTicketContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;

class UpdateDeliveryAddressTicket implements UpdateDeliveryAddressTicketContract
{
    public function handle(DeliveryTicket $deliveryTicket, array $inputs)
    {
        $validated = $this->validate($inputs);

        $deliveryTicket->update([
            'delivery_address_id' => $validated['delivery_address'],
        ]);
    }

    protected function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'delivery_address' => ['required'],
        ])->validate();
    }
}
