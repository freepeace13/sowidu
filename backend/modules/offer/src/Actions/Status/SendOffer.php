<?php

namespace Modules\Offer\Actions\Status;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Enums\OfferActionType;
use Modules\Offer\Events\OfferSent;
use Modules\Offer\Models\Offer;
use Modules\Offer\OfferService;

class SendOffer
{
    use AsAction;

    public function handle(Model $user, Model $company, Offer $offer): Offer|bool
    {
        Gate::forUser($user)->authorize('send', $offer);

        if ($offer->items()->doesntExist()) {
            flash_error(trans('offer.messages.failed.empty-offer'));

            return false;
        }

        $service = OfferService::make($offer);

        $service->send();

        $service->logAction(OfferActionType::SENT, $user);

        event(new OfferSent($offer));

        return $offer;
    }
}
