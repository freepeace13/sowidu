<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Actions;

use Illuminate\Support\Facades\Validator;
use Modules\DeliveryTicket\Contracts\Actions\UpdateDelivererTicketContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;

class UpdateDelivererTicket implements UpdateDelivererTicketContract
{
    public function handle(DeliveryTicket $deliveryTicket, array $inputs)
    {
        $validated = $this->validate($inputs);

        $deliveryTicket->update([
            'deliverer_id' => $validated['deliverer'],
        ]);
    }

    protected function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'deliverer' => ['required'],
        ])->validate();
    }
}
