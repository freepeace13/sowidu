<?php

namespace App\Contracts\Actions;

use App\Models\User;

interface UpdatesUserProfile
{
    public function update(User $user, array $inputs, $errorBag = null);
}
