<?php

namespace App\Contracts\Actions;

use App\Models\Company as Team;
use App\Models\User;

interface CreatesTeams
{
    public function create(User $user, array $data, $errorBag = null): Team;
}
