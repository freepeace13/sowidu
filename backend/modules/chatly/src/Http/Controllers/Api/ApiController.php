<?php

namespace Modules\Chatly\Http\Controllers\Api;

use Packages\RestApi\RestfulController;

class ApiController extends RestfulController
{
    protected function currentParticipant()
    {
        $participant = $this->currentUser();

        if ($currentTeam = $this->currentTeam()) {
            $participant = $participant->teamMembership($currentTeam);
        }

        return $participant;
    }
}
