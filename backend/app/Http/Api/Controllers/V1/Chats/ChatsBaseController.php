<?php

namespace App\Http\Api\Controllers\V1\Chats;

use Packages\RestApi\RestfulController;

class ChatsBaseController extends RestfulController
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
