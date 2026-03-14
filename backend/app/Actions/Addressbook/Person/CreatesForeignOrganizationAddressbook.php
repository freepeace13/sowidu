<?php

namespace App\Actions\Addressbook\Person;

use App\Actions\Addressbook\AddressbookAction;
use App\Contracts\Addressbook\Actions\CreateAddressbookAction;
use App\Events\Addressbook\AddressbookCreated;
use App\Models\Addressbook;
use App\Models\User;
use App\Traits\WithOrganizationEssentials;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreatesForeignOrganizationAddressbook extends AddressbookAction implements CreateAddressbookAction
{
    use WithOrganizationEssentials;

    public function create(User $user, array $inputs, $teamId = null): Addressbook
    {
        Gate::forUser($user)->authorize('create', Addressbook::class);

        $validated = Validator::make($inputs, array_merge($this->rules(), [
            'institution_type' => [
                'sometimes',
                Rule::in(Arr::pluck($this->institutionTypes(), 'name')),
            ],
            'legalform' => [
                'sometimes',
                Rule::in(Arr::pluck($this->legalForms(), 'name')),
            ],
        ]), $this->addressMessages())->validate();

        $addressbook = Addressbook::create(
            array_merge(
                $validated,
                [
                    'team_id' => $teamId,
                    'user_id' => $user->id,
                    'foreign_type' => Addressbook::FOREIGN_ORGANIZATION,
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
