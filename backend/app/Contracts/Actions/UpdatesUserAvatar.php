<?php

namespace App\Contracts\Actions;

use App\Models\User;

interface UpdatesUserAvatar
{
    public function update(User $user, $avatar, $errorBag = null);
}
