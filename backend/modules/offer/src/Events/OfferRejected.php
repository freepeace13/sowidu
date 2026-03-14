<?php

namespace Modules\Offer\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Offer\Models\Offer;

class OfferRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Offer $offer,
        public Model $causer,
        public ?Model $company = null,
    ) {}
}
