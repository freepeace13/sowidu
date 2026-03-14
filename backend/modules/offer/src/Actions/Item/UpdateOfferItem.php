<?php

namespace Modules\Offer\Actions\Item;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Events\OfferItemsUpdated;
use Modules\Offer\Models\Offer;
use Modules\Offer\Models\OfferItem;

class UpdateOfferItem
{
    use AsAction;

    public function handle(
        Model $user,
        Model $company,
        Offer $offer,
        OfferItem $offerItem,
        array $inputs,
    ): OfferItem {
        Gate::forUser($user)->authorize('manageItems', $offer);

        $validated = $this->validate($inputs);

        $offerItem->update([
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
        ]);

        event(new OfferItemsUpdated($offer));

        return $offerItem;
    }

    public function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'quantity' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
        ])->validate();
    }
}
