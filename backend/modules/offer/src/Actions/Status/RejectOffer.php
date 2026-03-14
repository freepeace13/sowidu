<?php

namespace Modules\Offer\Actions\Status;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Events\OfferRejected;
use Modules\Offer\Models\Offer;
use Modules\Offer\OfferService;

class RejectOffer
{
    use AsAction;

    public function handle(Model $user, Offer $offer, ?Model $company = null): Offer
    {
        Gate::forUser($user)->authorize('reject', $offer);

        OfferService::make($offer)->reject();

        event(new OfferRejected($offer, $user, $company));

        return $offer->refresh();
    }
}
