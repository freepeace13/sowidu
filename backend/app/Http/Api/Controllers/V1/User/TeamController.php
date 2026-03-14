<?php

namespace App\Http\Api\Controllers\V1\User;

use App\Actions\Organization\Employee\LeaveOnOrganization;
use App\Http\Api\Resources\V1\TeamResource;
use App\Models\Company as Team;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class TeamController extends RestfulController
{
    public function index(Request $request)
    {
        return TeamResource::collection(
            UserRepository::make($this->currentUser())->getCompanies(),
            fn (TeamResource $resource) => $resource->withType(),
        );
    }

    public function leave(Request $request, Team $team)
    {
        (new LeaveOnOrganization)->execute($this->currentUser(), $team);

        return $this->response();
    }
}
