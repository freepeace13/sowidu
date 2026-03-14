<?php

namespace App\Contracts\Actions;

use App\Models\Company as Team;
use App\Models\User;

interface SendsTeamInvitations
{
    public function send(User $user, Team $team, array $inputs, $errorBag = null);
}
