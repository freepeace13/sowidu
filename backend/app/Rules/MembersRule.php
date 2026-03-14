<?php

namespace App\Rules;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

class MembersRule implements Rule
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
        if (!is_multi_array($value)) {
            return false;
        }

        return collect($value)->every(function ($entry) {
            return $this->isValidEntry($entry);
        });
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }

    /**
     * @return bool
     */
    protected function isValidEntry(array $entry)
    {
        $allowedTypes = $this->allowedMemberableTypes();

        if (Arr::has($entry, ['id', 'type'])) {

            if (in_array($entry['type'], $allowedTypes)) {
                return (bool) resolve_array_morphs($entry);
            }

        }

        return false;
    }

    /**
     * @return array
     */
    protected function allowedMemberableTypes()
    {
        return [
            model_alias(User::class),
            model_alias(Employee::class),
        ];
    }
}
