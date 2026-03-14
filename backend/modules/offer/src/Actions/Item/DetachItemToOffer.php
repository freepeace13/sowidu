<?php

namespace Modules\Offer\Actions\Item;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Events\OfferItemsUpdated;
use Modules\Offer\Models\Offer;
use Modules\Offer\Models\OfferItem;

class DetachItemToOffer
{
    use AsAction;

    public function handle(Model $user, Model $company, Offer $offer, OfferItem $offerItem): void
    {
        // Authorization check
        Gate::forUser($user)->authorize('manageItems', $offerItem->offer);

        // Detach the item from the offer
        $offerItem->delete();

        event(new OfferItemsUpdated($offer));
    }
}
