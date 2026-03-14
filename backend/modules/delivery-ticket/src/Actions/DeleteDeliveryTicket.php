<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Actions;

use Illuminate\Support\Facades\Gate;
use Modules\DeliveryTicket\Contracts\Actions\DeleteDeliveryTicketContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;

class DeleteDeliveryTicket implements DeleteDeliveryTicketContract
{
    public function handle($user, $company, DeliveryTicket $deliveryTicket)
    {
        Gate::forUser($user)->authorize('delete', $deliveryTicket);

        $deliveryTicket->delete();
    }
}
