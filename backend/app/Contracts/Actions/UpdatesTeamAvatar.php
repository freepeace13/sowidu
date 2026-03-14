<?php

namespace App\Contracts\Actions;

use App\Models\Company as Team;

interface UpdatesTeamAvatar
{
    public function update(Team $team, $avatar, $errorBag = null);
}
