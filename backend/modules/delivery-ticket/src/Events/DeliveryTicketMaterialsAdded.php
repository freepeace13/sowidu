<?php

namespace Modules\DeliveryTicket\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryTicketMaterialsAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public $deliveryTicketMaterials, public $causer)
    {
        //
    }
}
