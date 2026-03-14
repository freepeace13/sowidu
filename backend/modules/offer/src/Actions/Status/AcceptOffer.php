<?php

namespace Modules\Offer\Actions\Status;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Events\OfferAccepted;
use Modules\Offer\Models\Offer;
use Modules\Offer\OfferService;

class AcceptOffer
{
    use AsAction;

    public function handle(Model $user, Offer $offer, ?Model $company = null): Offer
    {
        Gate::forUser($user)->authorize('accept', $offer);

        OfferService::make($offer)->accept();

        event(new OfferAccepted($offer, $user, $company));

        return $offer->refresh();
    }
}
