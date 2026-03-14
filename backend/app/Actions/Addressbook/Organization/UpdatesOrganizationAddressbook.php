<?php

namespace App\Actions\Addressbook\Organization;

use App\Actions\Addressbook\AddressbookAction;
use App\Events\Addressbook\AddressbookUpdated;
use App\Models\Addressbook;
use App\Models\User;
use App\Traits\WithOrganizationEssentials;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdatesOrganizationAddressbook extends AddressbookAction
{
    use WithOrganizationEssentials;

    public function update(User $user, Addressbook $addressbook, array $inputs, ?int $teamId = null)
    {
        Gate::forUser($user)->authorize('update', $addressbook);

        $validated = Validator::make($inputs, array_merge(
            $this->rules(),
            [
                'institution_type' => [
                    'sometimes',
                    Rule::in(Arr::pluck($this->institutionTypes(), 'name')),
                ],
                'legalform' => [
                    'sometimes',
                    Rule::in(Arr::pluck($this->legalForms(), 'name')),
                ],
            ],
        ), $this->addressMessages())->validate();

        $addressbook->update($validated);
        $addressbook->updatePlace($addressbook->currentPlace, $validated['address']);

        event(new AddressbookUpdated($user, $addressbook));

        return $addressbook;
    }
}
