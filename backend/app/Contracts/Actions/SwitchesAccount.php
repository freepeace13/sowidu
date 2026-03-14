<?php

namespace App\Contracts\Actions;

use App\Models\Company as Team;
use App\Models\User;

interface SwitchesAccount
{
    public function switch(User $user, $urn, $errorBag = null): ?Team;
}
