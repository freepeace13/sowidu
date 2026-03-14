<?php

namespace App\Http\Api\Controllers\V1\Media;

use Packages\RestApi\RestfulController;

class MediaController extends RestfulController
{
    protected function getHasMediaUser()
    {
        $user = $this->currentUser();

        if ($team = $this->currentTeam()) {
            $user = $user->teamMembership($team);
        }

        return $user;
    }
}
