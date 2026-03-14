<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Packages\Countries\Countries;

class CountryRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Countries::find($value) !== null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not valid country cca2 code.';
    }
}
