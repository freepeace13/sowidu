<?php

namespace App\Actions\Addressbook\Organization\Member;

use App\Actions\Addressbook\AddressbookAction;
use App\Actions\Addressbook\Person\UpdatesPersonAddressbook;
use App\Models\Addressbook;
use App\Models\User;
use App\Traits\WithOrganizationEssentials;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AddressbookAddOrganizationMember extends AddressbookAction
{
    use WithOrganizationEssentials;

    public function add(
        User $user,
        Addressbook $organization,
        array $inputs,
        ?int $teamId = null,
    ): Addressbook {
        Gate::forUser($user)->authorize('addMember', $organization);

        $validated = Validator::make($inputs, array_merge(
            $this->rules(),
            [
                'position' => [
                    'required',
                    'string',
                    Rule::in($this->allPositions()),
                ],
                'addressbook_id' => [
                    'required',
                    'integer',
                    'exists:addressbooks,id',
                ],
            ],
        ), $this->addressMessages())->validate();

        $personAddressbook = $this->getPersonAddressbook(
            $user,
            $validated['addressbook_id'],
        );

        (new UpdatesPersonAddressbook)->update(
            $user,
            $personAddressbook,
            Arr::except($validated, ['position', 'addressbook_id']),
            $teamId,
        );

        $organization->organizationMembers()
            ->attach(
                $personAddressbook,
                [
                    'position' => $validated['position'],
                ],
            );

        return $organization;
    }

    protected function getPersonAddressbook(User $user, int $personAddressbookId): Addressbook
    {
        $personAddressbook = Addressbook::ownedByUser($user)->find($personAddressbookId);

        // @todo remove after adding policy for updating addressbook
        throw_validation_unless($personAddressbook, 'You are not the owner of this data. Please refresh the page and try again.');

        return $personAddressbook;
    }
}
