<?php

namespace App\Contracts\Actions;

use App\Models\Company as Team;
use App\Models\Employee as TeamMember;
use App\Models\User;

interface UpdatesTeamMembersProfile
{
    public function update(User $actor, Team $team, TeamMember $member, array $inputs, $errorBag = null);
}
