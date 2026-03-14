<?php

namespace App\Actions;

use App\Events\Employment\EmployeeCreated;
use App\Models\Company;
use App\Models\Specialization;
use App\Models\User;
use App\Repositories\RoleRepository;

class CreateCompanyEmployee
{
    public function execute(Company $company, User $user, string $role)
    {
        $roles = RoleRepository::createFor($company);

        $employee = $company->getEmployee($user);

        if (is_null($employee)) {
            $specialization = Specialization::first();

            $employee = $company->employees()->create([
                'specialization_id' => $specialization->getKey(),
                'user_id' => $user->getKey(),
                'role' => $role,
                'confirmed' => true,
            ]);

            $employee->assignRole($role = $roles->firstOrCreate($role));

            event(new EmployeeCreated($employee));
        }

        return $employee;
    }
}
