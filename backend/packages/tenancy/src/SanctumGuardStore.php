<?php

namespace Packages\Tenancy;

use Illuminate\Database\Eloquent\Model;

class SanctumGuardStore implements TeamStore
{
    public function setTeamId(Model $user, $teamId): void
    {
        $user->forceFill([
            'current_team_id' => $teamId,
        ])->save();
    }

    public function getTeamId(Model $user): ?int
    {
        return $user->current_team_id;
    }
}
