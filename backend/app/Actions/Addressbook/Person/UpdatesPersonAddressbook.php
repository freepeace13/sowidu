<?php

namespace App\Actions\Addressbook\Person;

use App\Actions\Addressbook\AddressbookAction;
use App\Contracts\Addressbook\Actions\UpdateAddressbookAction;
use App\Events\Addressbook\AddressbookUpdated;
use App\Models\Addressbook;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdatesPersonAddressbook extends AddressbookAction implements UpdateAddressbookAction
{
    public function update(
        User $user,
        Addressbook $addressbook,
        array $inputs,
        $teamId = null,
    ): Addressbook {
        Gate::forUser($user)->authorize('update', $addressbook);

        $validated = Validator::make($inputs, array_merge($this->rules(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => ['required', 'email', 'string'],
            'name' => 'nullable',
        ]), $this->addressMessages())->validate();

        $addressbook->update(
            array_merge($validated, [
                'details' => $this->extractUserDetails($validated),
                'name' => $this->buildName($validated),
            ]),
        );

        $addressbook->updateOrCreatePlace($addressbook->name, $validated['address']);

        event(new AddressbookUpdated($user, $addressbook));

        return $addressbook;
    }
}
