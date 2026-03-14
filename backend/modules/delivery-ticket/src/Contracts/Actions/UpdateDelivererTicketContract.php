<?php

namespace Modules\DeliveryTicket\Contracts\Actions;

use Modules\DeliveryTicket\Models\DeliveryTicket;

interface UpdateDelivererTicketContract
{
    public function handle(DeliveryTicket $deliveryTicket, array $inputs);
}
