<?php

namespace App\Actions\Traits;

use Illuminate\Support\Arr;

trait HasAddressFields
{
    protected function parseCountryInput(array $inputs): array
    {
        return Arr::set($inputs, 'country', Arr::get($inputs, 'country.code'));
    }
}
