<?php

use Illuminate\Contracts\Validation\Rule;
use Packages\Addressable\Countries;

class StateRule implements Rule
{
    protected $country;

    public function __construct($country)
    {
        $this->country = $country;
    }

    public function passes($attribute, $value)
    {
        $country = Countries::where('cca3', $this->country)->first();

        $states = $country->hydrateStates()->states
            ->pluck('name')
            ->toArray();

        return in_array($value, $states);
    }

    public function message()
    {
        return 'The :attribute is not valid.';
    }
}
