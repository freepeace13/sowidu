<?php

namespace Modules\Offer\Controllers;

use Illuminate\Http\Request;
use Modules\Offer\Actions\Item\AttachManualItem;
use Modules\Offer\Http\Controllers\Controller;
use Modules\Offer\Models\Offer;

class OfferManualItemController extends Controller
{
    public function store(Request $request, Offer $offer)
    {
        AttachManualItem::run(
            $request->user(),
            $request->company(),
            $offer,
            $request->all(),
        );

        flash_success(__('offer.messages.items.manual_item_added'));

        return back();
    }
}
