<?php

namespace App\Actions\Rules;

use App\Actions\Traits\HasAddressFields;

trait WithAddressRules
{
    use HasAddressFields;

    protected function addressRules(): array
    {
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
                'sometimes',
                'required',
            ],
            'address.zipcode' => [
                'nullable',
            ],
        ];
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
