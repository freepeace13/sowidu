<?php

namespace App\Actions\AddressRecord;

use App\Actions\Rules\WithAddressRules;
use App\Actions\Traits\AsAction;
use App\Enums\Permissions;
use App\Models\Place;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class UpdateAddressRecord
{
    use AsAction;
    use WithAddressRules;

    public function handle(User $user, Place $place, array $inputs): Place
    {
        abort_unless(
            Permissions::isSuperAdmin($user),
            403,
        );

        $validated = Validator::make(
            $inputs,
            $this->addressRules(),
            $this->addressMessages(),
        )->validate();

        return tap($place)
            ->update(Arr::only($validated['address'], [
                'type',
                'house_number',
                'street',
                'state',
                'city',
                'country',
                'zipcode',
            ]));

    }
}
