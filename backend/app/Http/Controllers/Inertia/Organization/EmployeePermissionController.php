<?php

namespace App\Http\Controllers\Inertia\Organization;

use App\Actions\Organization\Role\UpdateEmployeeRolePermission;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Permission;
use App\Repositories\RoleRepository;
use App\Support\Facades\Impersonate;
use Illuminate\Http\Request;

class EmployeePermissionController extends InertiaController
{
    public function index($role)
    {
        $repository = RoleRepository::createFor(Impersonate::tenant());

        abort_unless($repository->hasRole($role), 404);

        $role = $repository->findByName($role);

        if ($this->shouldRespondJson()) {
            return response()->json(
                Permission::all()
                    ->mapWithKeys(function ($permission) use ($role) {
                        return [$permission->name => $role->hasDirectPermission($permission)];
                    })->toArray(),
            );
        }
    }

    public function store(Request $request) {}

    public function update(Request $request)
    {
        (new UpdateEmployeeRolePermission)->update(Impersonate::tenant(), $request->role, $request->permissions);

        return back(303);
    }
}
