<?php

namespace Modules\Offer\Listeners;

use Modules\Offer\Events\OfferItemsUpdated;
use Modules\Offer\OfferService;

class RecalculateOfferTotals
{
    public function handle(OfferItemsUpdated $event)
    {
        $offer = $event->offer;

        $service = OfferService::make($offer);

        $service->saveOfferTotals();
    }
}
