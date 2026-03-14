<?php

namespace Modules\DeliveryTicket\Contracts\Actions;

use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;

interface RemoveMaterialOnDeliveryTicketContract
{
    public function handle($user, $company, DeliveryTicket $deliveryTicket, DeliveryTicketMaterial $material);
}
