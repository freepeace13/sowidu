<?php

namespace App\Actions\AddressRecord;

use App\Actions\Place\AddNewCity;
use App\Actions\Place\AddNewState;
use App\Actions\Rules\WithAddressRules;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CreatesAddressRecord
{
    use WithAddressRules;

    public function create(User $user, array $inputs): \App\Models\Place
    {
        // TODO add Gate
        $validated = Validator::make(
            $inputs,
            $this->addressRules(),
            $this->addressMessages(),
        )->validate();

        $address = data_get($validated, 'address');

        AddNewCity::run(Arr::only($address, ['country', 'city']));

        AddNewState::run(Arr::only($address, ['state', 'country']));

        return $user->addPublicPlace($address);
    }
}
