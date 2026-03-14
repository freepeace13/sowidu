<?php

namespace App\Http\Api\Controllers\V1\Teams;

use App\Contracts\Actions\UpdatesTeamMembersProfile;
use App\Http\Api\Resources\V1\TeamMemberResource;
use App\Models\Company as Team;
use App\Models\Employee as TeamMember;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class UpdateTeamMembersProfileController extends RestfulController
{
    public function __construct(
        protected UpdatesTeamMembersProfile $updatesTeamMembersProfileAction,
    ) {}

    public function __invoke(Request $request, Team $team, TeamMember $member)
    {
        $actor = $this->currentUser();

        $updatedMember = $this->updatesTeamMembersProfileAction->update(
            $actor, $team, $member, $request->only([
                'rates',
                'contactNumber',
                'roles',
            ]),
        );

        return (new TeamMemberResource($updatedMember->user))
            ->withIsOwner($team)
            ->withRoles($team)
            ->withPermissions($team)
            ->withTeamRole($team)
            ->withTeamRate($team)
            ->withMembershipId($team);
    }
}
