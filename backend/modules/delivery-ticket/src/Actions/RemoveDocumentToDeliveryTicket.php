<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Actions;

use Illuminate\Support\Facades\Gate;
use Modules\DeliveryTicket\Contracts\Actions\RemoveDocumentToDeliveryTicketContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketDocument;

class RemoveDocumentToDeliveryTicket implements RemoveDocumentToDeliveryTicketContract
{
    public function handle($user, DeliveryTicket $deliveryTicket, DeliveryTicketDocument $document)
    {
        Gate::forUser($user)->authorize('update', $deliveryTicket);

        $document->delete();
    }
}
