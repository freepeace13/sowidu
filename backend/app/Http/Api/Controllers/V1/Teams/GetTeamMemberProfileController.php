<?php

namespace App\Http\Api\Controllers\V1\Teams;

use App\Http\Api\Resources\V1\TeamMemberResource;
use App\Models\Company as Team;
use App\Models\Employee as TeamMember;
use Illuminate\Support\Facades\Gate;
use Packages\RestApi\RestfulController;

class GetTeamMemberProfileController extends RestfulController
{
    public function __invoke(Team $team, TeamMember $member)
    {
        $user = $this->currentUser();

        Gate::forUser($user)->authorize('view', [$member, $team]);

        abort_unless($member->belongsToTeam($team), 404);

        return (new TeamMemberResource($member->user))
            ->withIsOwner($team)
            ->withRoles($team)
            ->withPermissions($team)
            ->withTeamRole($team)
            ->withTeamRate($team)
            ->withMembershipId($team);
    }
}
