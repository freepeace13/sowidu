<?php

namespace Modules\Offer\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Offer\Models\Offer;

class OfferItemsUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Offer $offer) {}
}
