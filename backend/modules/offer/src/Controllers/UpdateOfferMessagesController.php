<?php

namespace Modules\Offer\Controllers;

use Illuminate\Http\Request;
use Modules\Offer\Actions\UpdateOfferMessages;
use Modules\Offer\Http\Controllers\Controller;
use Modules\Offer\Models\Offer;

class UpdateOfferMessagesController extends Controller
{
    public function __invoke(Request $request, Offer $offer)
    {
        UpdateOfferMessages::run(
            $request->user(),
            $offer,
            $request->all(),
        );

        flash_success(
            __('offer.messages.subject-message-notes-updated'),
        );

        return back();
    }
}
