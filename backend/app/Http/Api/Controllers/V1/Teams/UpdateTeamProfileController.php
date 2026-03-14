<?php

namespace App\Http\Api\Controllers\V1\Teams;

use App\Contracts\Actions\UpdatesTeamProfile;
use App\Http\Api\Resources\V1\TeamResource;
use App\Models\Company as Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Packages\RestApi\RestfulController;

class UpdateTeamProfileController extends RestfulController
{
    public function __invoke(Request $request, Team $team, UpdatesTeamProfile $updater)
    {
        $user = $this->api()->user();

        Gate::forUser($user)->authorize('update', $team);

        $updated = $updater->update($team, [
            'name' => $request->name,
            'legalForm' => $request->legalForm,
            'institutionType' => $request->institutionType,
        ]);

        return new TeamResource($updated);
    }
}
