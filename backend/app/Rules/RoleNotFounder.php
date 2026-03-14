<?php

namespace App\Rules;

use App\Models\Company;
use Illuminate\Contracts\Validation\Rule;

class RoleNotFounder implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return strtolower($value) !== strtolower(Company::FOUNDER_ROLE_NAME);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        // TODO remove From Rule!
        return 'From Rule! This role name "Founder" is reserved. Please use another adjective for a role.';
    }
}
