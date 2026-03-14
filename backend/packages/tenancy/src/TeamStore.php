<?php

namespace Packages\Tenancy;

use Illuminate\Database\Eloquent\Model;

interface TeamStore
{
    public function setTeamId(Model $user, $teamId): void;

    public function getTeamId(Model $user): ?int;
}
