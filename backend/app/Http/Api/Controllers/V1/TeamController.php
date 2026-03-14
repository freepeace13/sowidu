<?php

namespace App\Http\Api\Controllers\V1;

use App\Http\Api\Resources\V1\TeamResource;
use Packages\RestApi\RestfulController;

class TeamController extends RestfulController
{
    public function index()
    {
        $user = $this->api()
            ->user();

        return TeamResource::collection(
            $user->teams,
            function (TeamResource $resource) use ($user) {
                $resource->withMembershipId($user);
            },
        );
    }
}
