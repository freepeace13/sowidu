<?php

namespace Modules\DeliveryTicket\Contracts\Actions;

use Modules\DeliveryTicket\Models\DeliveryTicket;

interface UpdateDeliveryTicketContract
{
    public function handle($user, $company, DeliveryTicket $deliveryTicket, array $inputs);
}
