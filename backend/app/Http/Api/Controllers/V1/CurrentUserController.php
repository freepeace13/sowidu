<?php

namespace App\Http\Api\Controllers\V1;

use App\Http\Api\Resources\V1\UserResource;
use Packages\RestApi\RestfulController;

class CurrentUserController extends RestfulController
{
    public function __invoke()
    {
        $user = $this->api()->user();

        return (new UserResource($user))
            ->withCurrentTeam()
            ->withRoles()
            ->withPermissions();
    }
}
