<?php

namespace App\Http\Api\Controllers\V1\Teams;

use App\Http\Api\Actions\Teams\SearchNewMembers;
use App\Http\Api\Resources\V1\UserResource;
use App\Models\Company as Team;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class SearchNewMemberController extends RestfulController
{
    public function __invoke(SearchNewMembers $action, Request $request, Team $team)
    {
        $results = $action->search(
            $this->currentUser(),
            $team,
            $request->query('q'),
            $request->query('limit', 3),
        );

        return UserResource::collection($results);
    }
}
