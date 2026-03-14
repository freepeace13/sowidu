<?php

namespace App\Contracts\Actions;

use App\Models\Company as Team;

interface UpdatesTeamProfile
{
    public function update(Team $team, array $inputs, $errorBag = null);
}
