<?php

namespace App\Actions\DeliveryTicket;

use App\Actions\Traits\AsAction;
use App\Models\DeliveryTicket;
use App\Models\DeliveryTicketDocument;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class RemoveDocumentToDeliveryTicket
{
    use AsAction;

    public function handle(User $user, DeliveryTicket $deliveryTicket, DeliveryTicketDocument $document)
    {
        Gate::forUser($user)->authorize('update', $deliveryTicket);

        $document->delete();
    }
}
