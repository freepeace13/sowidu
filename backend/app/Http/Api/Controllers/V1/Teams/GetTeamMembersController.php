<?php

namespace App\Http\Api\Controllers\V1\Teams;

use App\Http\Api\Resources\V1\TeamMemberResource;
use App\Models\Company as Team;
use Packages\RestApi\RestfulController;

class GetTeamMembersController extends RestfulController
{
    public function __invoke(Team $team)
    {
        $user = $this->currentUser();

        abort_unless($user->belongsToTeam($team), 404);

        $members = $team->employees()->with(['user'])->get();

        return TeamMemberResource::collection(
            $members->pluck('user'),
            fn (TeamMemberResource $resource) => $resource
                ->withIsOwner($team)
                ->withRoles($team)
                ->withTeamRole($team)
                ->withTeamRate($team)
                ->withMembershipId($team),
        );
    }
}
