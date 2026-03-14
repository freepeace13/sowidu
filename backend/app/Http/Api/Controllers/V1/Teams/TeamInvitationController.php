<?php

namespace App\Http\Api\Controllers\V1\Teams;

use App\Actions\Organization\Employee\InviteMemberOnOrganization;
use App\Http\Api\Resources\V1\TeamInvitationResource;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class TeamInvitationController extends RestfulController
{
    public function index(Request $request)
    {
        abort_unless($this->currentUserIsEmployee(), 403);

        return TeamInvitationResource::collection(
            $this->currentTeam()
                ->invitees()
                ->get(),
        );
    }

    public function store(Request $request)
    {
        $invitation = (new InviteMemberOnOrganization)->invite(
            $this->currentUser(),
            $this->currentTeam(),
            $request->all(),
        );

        if (blank($invitation)) {
            return response()->json([
                'message' => 'Invitation sent successfully',
            ]);
        }

        return new TeamInvitationResource($invitation);
    }

    public function destroy(Request $request)
    {
        //
    }
}
