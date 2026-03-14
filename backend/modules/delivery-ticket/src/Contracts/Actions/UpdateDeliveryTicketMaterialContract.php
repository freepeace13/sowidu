<?php

namespace Modules\DeliveryTicket\Contracts\Actions;

use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;

interface UpdateDeliveryTicketMaterialContract
{
    public function handle($user, $company, DeliveryTicket $deliveryTicket, DeliveryTicketMaterial $material, array $inputs);
}
