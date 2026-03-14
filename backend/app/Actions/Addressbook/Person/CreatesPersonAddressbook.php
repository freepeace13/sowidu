<?php

namespace App\Actions\Addressbook\Person;

use App\Actions\Addressbook\AddressbookAction;
use App\Contracts\Addressbook\Actions\CreateAddressbookAction;
use App\Events\Addressbook\AddressbookCreated;
use App\Models\Addressbook;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Packages\Urn\UrnManager;

class CreatesPersonAddressbook extends AddressbookAction implements CreateAddressbookAction
{
    public function create(User $user, array $inputs, $teamId = null): Addressbook
    {
        Gate::forUser($user)->authorize('create', Addressbook::class);

        $validated = Validator::make($inputs, array_merge($this->rules(), [
            'urn' => [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) {
                    [$resource] = UrnManager::parse($value);

                    if (!in_array($resource, ['person'])) {
                        $fail("The $attribute is not valid.");
                    }
                },
            ],
            'email' => ['required', 'email', 'string'],
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'name' => 'nullable',
        ]), $this->addressMessages())->validate();

        if (Arr::has($validated, 'urn')) {
            $resource = UrnManager::resolve($validated['urn']);
            $inputs = $this->extractResourceInputs($resource, $inputs);
        } else {
            // If the urn is not provided, then this is `Foreign Person`
            $inputs = [
                'foreign_type' => Addressbook::FOREIGN_PERSON,
                'name' => $this->buildName($validated),
            ];
        }

        if ($teamId instanceof Company) {
            $teamId = $teamId->id;
        }

        $addressbook = Addressbook::create(
            array_merge(
                $inputs,
                [
                    'team_id' => $teamId,
                    'user_id' => $user->id,
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
