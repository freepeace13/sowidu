<?php

namespace Modules\Offer\Controllers;

use Illuminate\Http\Request;
use Modules\Offer\Actions\Status\AcceptOffer;
use Modules\Offer\Actions\Status\CancelOffer;
use Modules\Offer\Actions\Status\RejectOffer;
use Modules\Offer\Actions\Status\SendOffer;
use Modules\Offer\Http\Controllers\Controller;
use Modules\Offer\Models\Offer;

class OfferStatusController extends Controller
{
    public function send(Request $request, Offer $offer)
    {
        $offer = SendOffer::run(
            $request->user(),
            $request->company(),
            $offer,
        );

        if ($offer) {
            flash_success(
                __('offer.messages.statuses.sent'),
            );
        }

        return back();
    }

    public function reject(Request $request, Offer $offer)
    {
        RejectOffer::run(
            $request->user(),
            $offer,
            $request->company(),
        );

        flash_success(
            __('offer.messages.statuses.rejected'),
        );

        return back();
    }

    public function accept(Request $request, Offer $offer)
    {
        AcceptOffer::run(
            $request->user(),
            $offer,
            $request->company(),
        );

        flash_success(
            __('offer.messages.statuses.accepted'),
        );

        return back();
    }

    public function cancel(Request $request, Offer $offer)
    {
        CancelOffer::run(
            $request->user(),
            $request->company(),
            $offer,
        );

        flash_success(
            __('offer.messages.statuses.cancelled'),
        );

        return back();
    }
}
