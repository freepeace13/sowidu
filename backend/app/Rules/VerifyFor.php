<?php

namespace App\Rules;

use App\Models\Verification;
use Illuminate\Contracts\Validation\Rule;

class VerifyFor implements Rule
{
    protected $purpose;

    public function __construct($purpose)
    {
        $this->purpose = $purpose;
    }

    public function passes($attribute, $value)
    {
        $resource = Verification::where('token', $value)->first();

        return is_array($this->purpose)
            ? in_array($resource->purpose, $this->purpose)
            : $this->purpose == $resource->purpose;
    }

    public function message()
    {
        return 'The verification token is not intended for ' . $this->purpose;
    }
}
