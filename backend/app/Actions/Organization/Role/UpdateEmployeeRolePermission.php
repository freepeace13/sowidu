<?php

namespace App\Actions\Organization\Role;

use App\Models\Company;
use App\Models\Permission;
use App\Repositories\RoleRepository;
use Illuminate\Support\Arr;

class UpdateEmployeeRolePermission
{
    public function update(Company $company, $role, array $permissions)
    {
        $repository = RoleRepository::createFor($company);

        $role = $repository->findByNameOrId($role);

        throw_validation_unless((bool) $role, 'This role does not exist.');

        if (Arr::isAssoc($permissions)) {
            $permissions = collect($permissions)
                ->filter(fn ($value) => (bool) $value)
                ->map(fn ($value, $name) => Permission::firstWhere('name', $name))
                ->values()
                ->pluck('id')
                ->toArray();
        }

        $role->syncPermissions($permissions);

        return $role;
    }
}
