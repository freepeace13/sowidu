<?php

namespace App\Http\Api\Controllers\V1\Teams;

use App\Http\Api\Resources\V1\TeamResource;
use App\Models\Company as Team;
use Packages\RestApi\RestfulController;

class TeamController extends RestfulController
{
    /** @deprecated */
    // public function store(Request $request)
    // {
    //     $creator = app(CreateTeam::class);

    //     $team = $creator->create($request->user(), $request->all());

    //     return new TeamResource($team);
    // }

    public function show(Team $team)
    {
        // TODO: Check authorizations

        return (new TeamResource($team))
            ->withRoles()
            ->withMembers();
    }
}
