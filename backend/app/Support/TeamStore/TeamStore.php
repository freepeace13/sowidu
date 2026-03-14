<?php

namespace App\Support\TeamStore;

use App\Models\User;

interface TeamStore
{
    public function setTeamId(User $user, $teamId): void;

    public function getTeamId(User $user): ?int;
}
