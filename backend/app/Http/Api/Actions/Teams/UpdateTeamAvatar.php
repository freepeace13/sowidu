<?php

namespace App\Http\Api\Actions\Teams;

use App\Contracts\Actions\UpdatesTeamAvatar;
use App\Events\OrganizationProfileUpdated;
use App\Models\Company as Team;
use Packages\RestApi\RestApiAction;

class UpdateTeamAvatar extends RestApiAction implements UpdatesTeamAvatar
{
    protected $rules = [
        'avatar' => ['required', 'image'],
    ];

    public function update(Team $team, $avatar, $errorBag = null): Team
    {
        $validated = $this->validate(compact('avatar'), $errorBag);

        $team->profile->setAvatar($validated['avatar']);

        $team->refresh();

        event(new OrganizationProfileUpdated($team));

        return $team;
    }
}
