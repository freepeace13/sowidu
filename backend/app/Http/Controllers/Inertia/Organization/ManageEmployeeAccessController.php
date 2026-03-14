<?php

namespace App\Http\Controllers\Inertia\Organization;

use App\Enums\Permissions;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Permission;
use App\Repositories\RoleRepository;
use App\Support\Facades\Impersonate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ManageEmployeeAccessController extends InertiaController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $role = $request->get('role');
        if ($role) {
            $repository = RoleRepository::createFor(Impersonate::tenant());

            abort_unless($repository->hasRole($role), 404);

            $role = $repository->findByName($role);
        }

        return Inertia::render('Account/Permission', [
            'role' => $request->query('role'),

            'permissions' => collect(Permissions::groupings())
                ->map(fn ($group, $key) => [
                    ...$group,
                    'permissions' => collect($group['permissions'])->mapWithKeys(fn ($permission) => [
                        $permission => Str::of($permission)->replace('_', ' ')
                            ->ucfirst()
                            ->toString(),
                    ]),
                ]),

            'rolePermissions' => fn () => !$role ? collect([]) : Permission::all()
                ->mapWithKeys(fn ($permission) => [
                    $permission->name => $role->hasDirectPermission($permission),
                ])
                ->toArray(),

            'roles' => $this->retrieveOrganizationRoles(),
        ]);
    }
}
