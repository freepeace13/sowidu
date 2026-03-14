<?php

namespace App\Http\Api\Controllers\V1\Teams;

use App\Models\Company as Team;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class CancelPendingInvitationController extends RestfulController
{
    public function __invoke(Request $request, Team $team)
    {
        return $this->response([
            'message' => 'Not yet implemented',
        ]);
    }
}
