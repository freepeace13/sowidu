<?php

namespace Modules\Offer\Middleware;

use Closure;
use Illuminate\Http\Request;
use Impersonate;
use Modules\Offer\OfferService;

class EnsureOfferParticipant
{
    public function handle(Request $request, Closure $next)
    {
        $offer = $request->route('offer');
        abort_if(blank($offer), 404, __('offer.messages.failed.not-found'));

        $offerService = OfferService::make($offer);

        if (!$offerService->isParticipant($request->user(), Impersonate::tenant())) {
            abort(403, __('offer.messages.failed.not-found'));
        }

        return $next($request);
    }
}
