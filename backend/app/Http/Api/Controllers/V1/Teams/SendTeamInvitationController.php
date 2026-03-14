<?php

namespace App\Http\Api\Controllers\V1\Teams;

use App\Contracts\Actions\SendsTeamInvitations;
use App\Models\Company as Team;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Packages\RestApi\RestfulController;

class SendTeamInvitationController extends RestfulController
{
    public function __invoke(Request $request, Team $team, SendsTeamInvitations $sender)
    {
        $user = $this->currentUser();

        abort_unless($user->belongsToTeam($team), 403);

        $sender->send($this->currentUser(), $team, [
            'email' => $request->email,
            'note' => $request->message,
            'role' => $request->role,
        ]);

        return $this->response(null, Response::HTTP_CREATED);
    }
}
