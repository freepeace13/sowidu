<?php

namespace App\Actions\Organization;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Rules\RoleNotFounder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateRole
{
    public function execute(User $user, Company $company, array $params): Role
    {
        $repository = RoleRepository::createFor($company);

        $validated = Validator::make($params, [
            'name' => [
                'required',
                Rule::notIn($repository->allRoles()
                    ->pluck('name')
                    ->toArray()),
                new RoleNotFounder,
            ],
        ])->validated();

        return $repository->firstOrCreate($validated['name']);
    }
}
