<?php

use Illuminate\Contracts\Validation\Rule;
use Packages\Addressable\Countries;

class CountryRule implements Rule
{
    public function passes($attribute, $value)
    {
        $countries = Countries::all()
            ->pluck('name.common')
            ->toArray();

        return in_array($value, $countries);
    }

    public function message()
    {
        return 'The :attribute is not valid.';
    }
}
