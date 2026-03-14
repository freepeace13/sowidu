<?php

namespace Modules\DeliveryTicket\Contracts\Services;

use Modules\DeliveryTicket\Models\DeliveryTicket;

interface DeliveryTicketMaterialServiceContract
{
    public static function make(DeliveryTicket $deliveryTicket): self;

    public function forUser($user): self;

    public function forCompany($company): self;

    public function addMaterials(array $catalogItems);
}
