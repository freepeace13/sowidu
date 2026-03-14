<?php

namespace Modules\DeliveryTicket\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;

class DeliveryTicketMaterialsUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public DeliveryTicketMaterial $deliveryTicketMaterial) {}
}
