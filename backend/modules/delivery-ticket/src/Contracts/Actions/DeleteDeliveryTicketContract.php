<?php

namespace Modules\DeliveryTicket\Contracts\Actions;

use Modules\DeliveryTicket\Models\DeliveryTicket;

interface DeleteDeliveryTicketContract
{
    public function handle($user, $company, DeliveryTicket $deliveryTicket);
}
