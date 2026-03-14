<?php

namespace App\Events\DeliveryTicket;

use App\Models\DeliveryTicketMaterial;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryTicketMaterialsUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public DeliveryTicketMaterial $deliveryTicketMaterial) {}
}
