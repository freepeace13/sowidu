<?php

namespace Modules\DeliveryTicket\Contracts\Actions;

use Modules\DeliveryTicket\Models\DeliveryTicket;

interface UpdateDeliveryAddressTicketContract
{
    public function handle(DeliveryTicket $deliveryTicket, array $inputs);
}
