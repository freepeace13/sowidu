<?php

namespace App\Rules;

use App\Models\User;
use Auth;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class EmailUniqueIfMismatch implements Rule
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
        if (!$value) {
            return true;
        }

        return Str::is(Auth::user()->email, $value) ?: !User::where('email', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is already taken.';
    }
}
