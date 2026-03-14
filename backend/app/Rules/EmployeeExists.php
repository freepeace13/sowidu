<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class EmployeeExists implements Rule
{
    public function passes($attribute, $value)
    {
        if (!helper('Auth::companyCheck')) {
            return false;
        }

        $user = User::find($value);

        return !helper('Auth::company')->isEmployed($user);
    }

    public function message()
    {
        return 'Entry might be one of your employees or you are not logged in at business account';
    }
}
