<?php

namespace Modules\Offer\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Models\Offer;

class UpdateOfferMessages
{
    use AsAction;

    public function handle(Model $user, Offer $offer, array $inputs): Offer
    {
        Gate::forUser($user)->authorize('update', $offer);

        $validated = $this->validate($inputs);

        return tap($offer)->update($validated);
    }

    public function validate(array $inputs): array
    {
        return Validator::make(
            $inputs,
            [
                'subject' => 'nullable|string|max:255',
                'message' => 'nullable|string',
                'notes' => 'nullable|string',
            ],
        )->validate();
    }
}
