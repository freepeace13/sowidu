<?php

namespace App\Actions\Addressbook;

use App\Models\Company;
use App\Models\Company as Organization;
use App\Models\User;
use App\Rules\CountryRule;
use Illuminate\Support\Arr;
use Packages\Urn\UrnManager;

class AddressbookAction
{
    protected bool $addressNullable = false;

    protected function getResource(string $urn)
    {
        return UrnManager::resolve($urn);
    }

    protected function extractResourceInputs($resource, array $inputs = [])
    {
        $inputs = match (get_class($resource)) {
            Organization::class => $this->extractOrganizationInputs($resource, $inputs),
            User::class => $this->extractUserInputs($resource, $inputs),
        };

        return $inputs;
    }

    protected function extractAddress(array $addressInputs, User|Company $resource): array
    {
        return [
            'street' => $addressInputs['street'] ?? null,
            'state' => $addressInputs['state'] ?? null,
            'city' => $addressInputs['city'] ?? null,
            'country' => $addressInputs['country'] ?? null,
            'house_number' => $addressInputs['house_number'] ?? null,
            'zipcode' => $addressInputs['zipcode'] ?? null,
            'model_id' => $resource->getKey(),
            'model_type' => $resource->getMorphClass(),
        ];
    }

    protected function extractUserInputs($user, array $inputs = [])
    {
        return [
            'name' => $this->extractUserName($user, $inputs),
            'photo' => $inputs['photo'] ?? get_user_avatar_url($user),
            'email' => $inputs['email'] ?? $user->email,
            'phone' => $inputs['phone'] ?? null,
            'model_id' => $user->getKey(),
            'model_type' => $user->getMorphClass(),
        ];
    }

    protected function extractUserName(User $user, array $inputs): string
    {
        return $this->buildName([
            'first_name' => $inputs['first_name'] ?? $user->first_name,
            'last_name' => $inputs['last_name'] ?? $user->last_name,
        ]);
    }

    public function buildName(array $inputs): string
    {
        return implode(' ', [$inputs['first_name'], $inputs['last_name']]);
    }

    protected function extractUserDetails(array $inputs)
    {
        return Arr::only($inputs, ['first_name', 'last_name']);
    }

    protected function extractOrganizationInputs($organization, array $inputs = [])
    {
        return [
            'name' => $inputs['name'] ?? $organization->name,
            'photo' => $inputs['photo'] ?? get_company_avatar_url($organization),
            'phone' => $inputs['phone'] ?? null,
            'model_id' => $organization->getKey(),
            'model_type' => $organization->getMorphClass(),
            'legalform' => $inputs['legalform'] ?? null,
            'institution_type' => $inputs['institution_type'] ?? null,
        ];
    }

    protected function rules(): array
    {
        return array_merge([
            'name' => [
                'string',
                'required',
            ],
            'email' => [
                'string',
                'email',
            ],
            'first_name' => [
                'string',
            ],
            'last_name' => [
                'string',
            ],
            'photo' => [
                'sometimes',
                'string',
            ],
            'phone' => ['nullable'],
        ], $this->addressRules());
    }

    protected function addressRules(): array
    {
        if ($this->addressNullable) {
            return [];
        }

        return [
            'address' => ['required', 'array'],
            'address.house_number' => [
                'nullable',
                'string',
            ],
            'address.street' => [
                'nullable',
                'string',
            ],
            'address.city' => [
                'required',
                'string',
            ],
            'address.state' => [
                'required',
                'string',
            ],
            'address.country' => [
                'required',
                'string',
                new CountryRule,
            ],
            'address.zipcode' => [
                'nullable',
            ],
        ];
    }

    public function allowAddressNullable()
    {
        $this->addressNullable = true;

        return $this;
    }

    /**
     * @return array|string[]
     */
    protected function addressMessages(): array
    {
        return [
            'address.city.required' => 'The city field is required.',
            'address.state.required' => 'The state field is required.',
            'address.country.required' => 'The country field is required.',
        ];
    }
}
