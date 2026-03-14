<?php

namespace App\Http\Api\Actions\Teams;

use App\Contracts\Actions\RunsTeamInvitationLinkActions;
use App\Models\Company as Team;
use App\Models\CompanyInvitation;
use App\Models\User;
use Closure;
use Packages\RestApi\RestApiAction;

class TeamInvitationLinkAction extends RestApiAction implements RunsTeamInvitationLinkActions
{
    public function run(Team $team, array $inputs): Closure
    {
        return function () use ($team, $inputs) {
            $user = User::create(['email' => $inputs['email']]);
            $user->markEmailAsVerified();

            CompanyInvitation::where('email', $user->email)->delete();

            CompanyInvitation::create([
                'email' => $user->email,
                'company_id' => $team->getKey(),
                'note' => $inputs['note'],
                'role' => $inputs['role'],
            ]);
        };
    }
}
