<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ContactExist implements Rule
{
    protected $typeField;

    public function __construct(string $typeField)
    {
        $this->typeField = $typeField;
    }

    public function passes($attribute, $value)
    {
        $type = request()->get($this->typeField);
        $model = 'App\\Models\\' . ucfirst($type);
        $instance = $model::find($value);

        if (!$instance) {
            return false;
        }

        return auth()->user()
            ->contacts()
            ->where('contactable_type', $instance->getMorphClass())
            ->where('contactable_id', $instance->id)
            ->exists();
    }

    public function message()
    {
        return 'The contact is invalid.';
    }
}
