<?php

namespace App\Actions\Organization;

use App\Models\Company;
use App\Models\Permission;
use App\Repositories\RoleRepository;

class AssignFounderPermissions
{
    public function execute(Company $company)
    {
        $employer = $company->founder;

        $roleRepository = RoleRepository::createFor($company);

        $permissionIds = Permission::all('id')->pluck('id');

        $employer->roles->each(function ($role) use ($roleRepository, $permissionIds) {
            $role = $roleRepository->findByName($role->name);
            $role->syncPermissions($permissionIds);
        });

        $employer->givePermissionTo($permissionIds->toArray());
    }
}
