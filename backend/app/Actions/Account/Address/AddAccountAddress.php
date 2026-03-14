<?php

namespace App\Actions\Account\Address;

use App\Actions\Rules\WithAddressRules;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AddAccountAddress
{
    use WithAddressRules;

    public function add(User $user, array $inputs, ?Company $team)
    {
        $validated = Validator::make(
            $inputs,
            array_merge($this->addressRules(), [
                'label' => 'required|string|min:3',
            ]),
            $this->addressMessages(),
        )->validate();

        $account = $team ?? $user;

        return $account->insertPlace(
            $validated['label'],
            $this->parseCountryInput($validated['address']),
        );
    }
}
