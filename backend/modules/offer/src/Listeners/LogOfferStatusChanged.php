<?php

namespace Modules\Offer\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Offer\Enums\OfferActionType;
use Modules\Offer\Events\OfferAccepted;
use Modules\Offer\Events\OfferRejected;
use Modules\Offer\OfferService;

class LogOfferStatusChanged implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OfferAccepted|OfferRejected $event): void
    {
        $offer = $event->offer;
        $user = $event->causer;

        $status = match (true) {
            $event instanceof OfferAccepted => OfferActionType::ACCEPTED,
            $event instanceof OfferRejected => OfferActionType::REJECTED,
        };

        OfferService::make($offer)->logAction($status, $user);
    }
}
