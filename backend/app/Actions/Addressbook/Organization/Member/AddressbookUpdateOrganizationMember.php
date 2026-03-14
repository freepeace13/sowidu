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

class AddressbookUpdateOrganizationMember extends AddressbookAction
{
    use WithOrganizationEssentials;

    public function update(
        User $user,
        Addressbook $organization,
        Addressbook $member,
        array $inputs,
        ?int $teamId = null,
    ) {
        Gate::forUser($user)->authorize('updateMember', [$organization, $member]);

        $validated = Validator::make($inputs, array_merge(
            $this->rules(),
            [
                'position' => [
                    'required',
                    'string',
                    Rule::in($this->allPositions()),
                ],
            ],
        ), $this->addressMessages())->validate();

        (new UpdatesPersonAddressbook)->update(
            $user,
            $member,
            Arr::except($validated, ['position']),
            $teamId,
        );

        $organization->organizationMembers()
            ->updateExistingPivot(
                $member,
                [
                    'position' => $validated['position'],
                ],
            );

        return $organization;
    }
}
