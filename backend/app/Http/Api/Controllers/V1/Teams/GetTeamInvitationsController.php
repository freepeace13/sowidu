<?php

namespace App\Http\Api\Controllers\V1\Teams;

use App\Http\Api\Resources\V1\TeamInvitationResource;
use App\Models\Company as Team;
use Packages\RestApi\RestfulController;

class GetTeamInvitationsController extends RestfulController
{
    public function __invoke(Team $team)
    {
        $user = $this->currentUser();

        abort_unless($user->belongsToTeam($team), 403);

        return TeamInvitationResource::collection(
            $team->invitees()->get(),
            fn (TeamInvitationResource $resource) => $resource->withPhoto(),
        );
    }
}
