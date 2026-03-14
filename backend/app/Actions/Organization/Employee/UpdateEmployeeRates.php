<?php

namespace App\Actions\Organization\Employee;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateEmployeeRates
{
    use AsAction;

    public function handle(User $user, Company $company, Employee $employee, array $inputs): Employee
    {
        Gate::forUser($user)->authorize('manageEmployeeRates', $company);

        $validated = $this->validate($inputs);

        // Save employee rate
        $employee->rate()
            ->updateOrCreate(
                ['employee_id' => $employee->id],
                $validated,
            );

        return $employee;
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'rate' => [
                'required',
                'numeric',
            ],
        ])->validate();
    }
}
