<?php

namespace App\Actions;

use App\Models\User;
use App\Rules\CountryRule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdatesAccountCurrentAddress
{
    public function update(User $user, array $input, $teamId = null)
    {
        $account = $teamId ? $user->teams()->find($teamId) : $user;

        if ($teamId) {
            Gate::forUser($user)->authorize('updateAddress', $account);
        }

        $validated = Validator::make($input, [
            'house_number' => ['nullable', 'string'],
            'street' => ['nullable', 'string'],
            'city' => ['string'],
            'state' => ['string'],
            'country' => ['required', new CountryRule],
            'zipcode' => ['nullable'],
        ])->validate();

        if ($currentPlace = $account->currentPlace) {
            $account->updatePlace($currentPlace, $validated);
        } else {
            $account->addPlace($account->fullName ?? $account->name, $validated);
        }
    }
}
