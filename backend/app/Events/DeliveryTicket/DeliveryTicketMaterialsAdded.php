<?php

namespace App\Events\DeliveryTicket;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryTicketMaterialsAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public $deliveryTicketMaterials, public User $causer)
    {
        //
    }
}
