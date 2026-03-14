<?php

namespace App\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Packages\Urn\UrnManager;

class UrnRule implements Rule
{
    protected $only;

    public function __construct(array $only = [])
    {
        $this->only = $only;
    }

    public function passes($attribute, $value)
    {
        try {
            [$resource] = UrnManager::parse($value);

            if (!$this->isResourceAllowed($resource)) {
                return false;
            }

            return !is_null(UrnManager::resolve($value));
        } catch (Exception $e) {
            return false;
        }
    }

    protected function isResourceAllowed($resource)
    {
        if (count($this->only) === 0) {
            return true;
        }

        return in_array(UrnManager::namespace($resource), $this->only) ||
            in_array(UrnManager::resource($resource), $this->only);
    }

    public function message()
    {
        return 'The :attribute is not valid';
    }
}
