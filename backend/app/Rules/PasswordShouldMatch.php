<?php

namespace App\Rules;

use Hash;
use Illuminate\Contracts\Validation\Rule;

class PasswordShouldMatch implements Rule
{
    /**
     * Holds the password
     *
     * @var string
     */
    protected $password;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $password)
    {
        $this->password = $password;
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
        return Hash::check($value, $this->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is invalid.';
    }
}
