<?php

namespace App\Models\Concerns;

use App\Models\Board;
use App\Services\Impersonate;

trait HasBoards
{
    public function boards()
    {
        $relation = $this->hasMany(Board::class, 'user_id');

        if (!$teamId = Impersonate::getTeamId()) {
            return $relation->whereNull('team_id');
        }

        return $relation->where('team_id', $teamId);
    }
}
