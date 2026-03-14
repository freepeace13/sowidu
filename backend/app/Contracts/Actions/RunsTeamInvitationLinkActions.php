<?php

namespace App\Contracts\Actions;

use App\Models\Company as Team;
use Closure;

interface RunsTeamInvitationLinkActions
{
    public function run(Team $team, array $inputs): Closure;
}
