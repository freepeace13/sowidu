<?php

namespace Modules\Offer\Controllers;

use Illuminate\Http\Request;
use Modules\Offer\Actions\Item\AttachItemToOffer;
use Modules\Offer\Actions\Item\DetachItemToOffer;
use Modules\Offer\Actions\Item\UpdateOfferItem;
use Modules\Offer\Http\Controllers\Controller;
use Modules\Offer\Models\Offer;
use Modules\Offer\Models\OfferItem;

class OfferItemController extends Controller
{
    public function store(Request $request, Offer $offer)
    {
        AttachItemToOffer::run(
            $request->user(),
            $request->company(),
            $offer,
            $request->all(),
        );

        flash_success(__('offer.messages.items.added'));

        return back();
    }

    public function update(Request $request, Offer $offer, OfferItem $item)
    {
        UpdateOfferItem::run(
            $request->user(),
            $request->company(),
            $offer,
            $item,
            $request->all(),
        );

        flash_success(__('offer.messages.items.updated'));

        return back();
    }

    public function destroy(Request $request, Offer $offer, OfferItem $item)
    {
        DetachItemToOffer::run(
            $request->user(),
            $request->company(),
            $offer,
            $item,
        );

        flash_success(__('offer.messages.items.removed'));

        return back();
    }
}
