<?php

namespace App\Actions\Organization\Employee;

use App\Actions\Traits\AsAction;
use App\Enums\Permissions;
use App\Events\Organization\EmployeeRolesUpdated;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateEmployeeRoles
{
    use AsAction;

    public function handle(User $user, Company $company, Employee $employee, array $inputs): Employee
    {
        $authEmployee = $company->getEmployee($user);

        $validate = Validator::make($inputs, [
            'roles' => ['required', 'array', Rule::in($this->getCompanyRoles($company))],
        ])->validate();

        // Validate if current user is valid to take this action
        throw_flash_unless(
            $authEmployee->hasPermissionTo(Permissions::CAN_MANAGE_PERMISSIONS),
            trans('validation.403'),
        );

        $previousRoles = $employee->roles()->get(['name'])->pluck('name')->toArray();

        $employee->syncRoles($validate['roles']);

        event(new EmployeeRolesUpdated($employee, $previousRoles));

        return $employee;
    }

    protected function getCompanyRoles(Company $company): array
    {
        return RoleRepository::createFor($company)
            ->allRoles()
            ->pluck('name')
            ->toArray();
    }
}
