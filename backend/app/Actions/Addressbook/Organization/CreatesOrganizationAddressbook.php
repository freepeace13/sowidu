<?php

namespace App\Actions\Addressbook\Organization;

use App\Actions\Addressbook\AddressbookAction;
use App\Events\Addressbook\AddressbookCreated;
use App\Models\Addressbook;
use App\Models\User;
use App\Traits\WithOrganizationEssentials;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Packages\Urn\UrnManager;

class CreatesOrganizationAddressbook extends AddressbookAction
{
    use WithOrganizationEssentials;

    public function create(User $user, array $inputs, ?int $teamId = null): Addressbook
    {
        Gate::forUser($user)->authorize('create', Addressbook::class);

        $validated = Validator::make($inputs, array_merge($this->rules(), [
            'urn' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    [$resource] = UrnManager::parse($value);

                    if (!in_array($resource, ['organization'])) {
                        $fail("The $attribute is not valid.");
                    }
                },
            ],
            'institution_type' => [
                'sometimes',
                Rule::in(Arr::pluck($this->institutionTypes(), 'name')),
            ],
            'legalform' => [
                'sometimes',
                Rule::in(Arr::pluck($this->legalForms(), 'name')),
            ],
        ]), $this->addressMessages())->validate();

        $resource = UrnManager::resolve($validated['urn']);
        $inputs = $this->extractResourceInputs($resource, $inputs);

        $addressbook = Addressbook::create(
            array_merge(
                $inputs,
                [
                    'team_id' => $teamId,
                    'user_id' => $user->id,
                ],
            ),
        );

        $addressbook->addPlace($addressbook->name, $validated['address']);

        event(new AddressbookCreated($user, $addressbook, $teamId));

        return $addressbook;
    }
}
