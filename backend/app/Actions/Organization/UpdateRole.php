<?php

namespace App\Actions\Organization;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateRole
{
    public function execute(User $user, Company $company, array $params): Role
    {
        $repository = RoleRepository::createFor($company);
        $allRoles = $repository->allRoles()
            ->pluck('name')
            ->toArray();

        $validated = Validator::make($params, [
            'old_name' => [
                'required',
                Rule::in($allRoles),
            ],
            'name' => [
                'required',
                Rule::notIn($allRoles),
            ],
        ])->validated();

        return tap($repository->findByName($validated['old_name']))
            ->update([
                'name' => $validated['name'],
            ]);
    }
}
