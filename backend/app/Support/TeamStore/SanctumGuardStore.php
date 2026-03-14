<?php

namespace App\Support\TeamStore;

use App\Models\User;

class SanctumGuardStore implements TeamStore
{
    public function setTeamId(User $user, $teamId): void
    {
        $user->forceFill([
            'current_team_id' => $teamId,
        ])->save();
    }

    public function getTeamId(User $user): ?int
    {
        return $user->current_team_id;
    }
}
