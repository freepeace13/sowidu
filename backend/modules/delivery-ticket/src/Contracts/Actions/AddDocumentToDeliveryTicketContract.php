<?php

namespace Modules\DeliveryTicket\Contracts\Actions;

use Modules\DeliveryTicket\Models\DeliveryTicket;

interface AddDocumentToDeliveryTicketContract
{
    public function handle($user, DeliveryTicket $deliveryTicket, array $inputs);
}
