<?php

namespace Modules\Offer\Actions\Status;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Enums\OfferActionType;
use Modules\Offer\Models\Offer;
use Modules\Offer\OfferService;

class CancelOffer
{
    use AsAction;

    public function handle(Model $user, Model $company, Offer $offer): Offer
    {
        Gate::forUser($user)->authorize('cancel', $offer);

        $service = OfferService::make($offer);

        $service->cancel();

        $service->logAction(OfferActionType::CANCELLED, $user);

        return $offer->refresh();
    }
}
