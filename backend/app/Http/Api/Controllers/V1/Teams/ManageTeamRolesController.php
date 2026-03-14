<?php

namespace App\Http\Api\Controllers\V1\Teams;

use App\Actions\Organization\CreateRole;
use App\Actions\Organization\Role\UpdateEmployeeRolePermission;
use App\Actions\Organization\UpdateRole;
use App\Http\Api\Resources\V1\RoleResource;
use App\Models\Company as Team;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class ManageTeamRolesController extends RestfulController
{
    public function index(Team $team)
    {
        $allRoles = RoleRepository::createFor($team)->allRoles();

        return RoleResource::collection($allRoles);
    }

    public function show(Team $team, int $role)
    {
        $repository = RoleRepository::createFor($team);

        $role = $repository->findByNameOrId($role);

        abort_unless((bool) $role, 404, 'This role does not exist.');

        return (new RoleResource($role))->withPermissions();
    }

    public function store(Request $request, Team $team)
    {
        $creator = new CreateRole;

        $newRole = $creator->execute($this->currentUser(), $team, $request->all());

        return new RoleResource($newRole);
    }

    public function update(Request $request, Team $team)
    {
        $updater = new UpdateRole;

        $updatedRole = $updater->execute(
            $this->currentUser(), $team, $request->all(),
        );

        return new RoleResource($updatedRole);
    }

    public function updatePermissions(Request $request, Team $team, int $role)
    {
        $updater = (new UpdateEmployeeRolePermission);

        if (is_string($permissions = $request->permissions)) {
            $permissions = json_decode($request->permissions, true);
        }

        $updatedRole = $updater->update($team, $role, $permissions);

        return (new RoleResource($updatedRole))->withPermissions();
    }
}
