<?php

namespace App\Rules;

use App\Models\Employee;
use App\Models\User;
use Auth;
use Illuminate\Contracts\Validation\Rule;

class ContactOwner implements Rule
{
    protected $typeField;

    protected $value;

    public function __construct(string $typeField)
    {
        $this->typeField = $typeField;
    }

    public function passes($attribute, $value)
    {
        $this->value = $value;

        return $this->validate();
    }

    public function message()
    {
        return 'You cannot add your own card.';
    }

    private function validate()
    {
        $account = Auth::user();
        $instance = $this->getTypeInstance();

        if (helper('Auth::companyCheck')) {
            $employee = $account
                ->employees()
                ->where('user_id', $account->user->id)
                ->first();

            if ($instance instanceof Employee) {
                return !$instance->is($employee);
            } elseif ($instance instanceof User) {
                return !$instance->is($account->user);
            }
        }

        return !$instance->is($account);
    }

    private function getType()
    {
        $type = request()->get($this->typeField);

        return 'App\\Models\\' . ucfirst($type);
    }

    private function getTypeInstance()
    {
        $instance = $this->getType();

        return $instance::find($this->value);
    }
}
