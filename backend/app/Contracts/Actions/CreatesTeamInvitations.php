<?php

namespace App\Contracts\Actions;

use App\Models\Company as Team;
use App\Models\CompanyInvitation;
use App\Models\User;

interface CreatesTeamInvitations
{
    public function create(User $user, Team $team, array $inputs, $errorBag = null): CompanyInvitation;
}
