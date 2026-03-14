<?php

namespace Modules\DeliveryTicket\Contracts\Actions;

use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketDocument;

interface RemoveDocumentToDeliveryTicketContract
{
    public function handle($user, DeliveryTicket $deliveryTicket, DeliveryTicketDocument $document);
}
