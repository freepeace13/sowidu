<?php

namespace App\Actions\Addressbook\Person;

use App\Actions\Addressbook\AddressbookAction;
use App\Contracts\Addressbook\Actions\CreateAddressbookAction;
use App\Events\Addressbook\AddressbookCreated;
use App\Models\Addressbook;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class CreatesForeignPersonAddressbook extends AddressbookAction implements CreateAddressbookAction
{
    public function create(User $user, array $inputs, $teamId = null): Addressbook
    {
        Gate::forUser($user)->authorize('create', Addressbook::class);

        $validated = Validator::make($inputs, array_merge($this->rules(), [
            'email' => ['required', 'email', 'string'],
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'name' => 'nullable',
        ]), $this->addressMessages())->validate();

        $addressbook = Addressbook::create(
            array_merge(
                $validated,
                [
                    'name' => $this->buildName($validated),
                    'team_id' => $teamId,
                    'user_id' => $user->id,
                    'foreign_type' => Addressbook::FOREIGN_PERSON,
                    'details' => $this->extractUserDetails($validated),
                ],
            ),
        );

        if ($address = $validated['address'] ?? null) {
            $addressbook->addPlace($addressbook->name, $address);
        }

        event(new AddressbookCreated($user, $addressbook, $teamId));

        return $addressbook;
    }
}
