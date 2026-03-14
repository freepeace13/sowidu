<?php

namespace App\Support\TeamStore;

use App\Models\User;
use Illuminate\Support\Facades\Session as SessionStore;

class WebGuardStore implements TeamStore
{
    public function setTeamId(User $user, $teamId): void
    {
        SessionStore::put($this->sessionKey($user), $teamId);
    }

    public function getTeamId(User $user): ?int
    {
        return SessionStore::get($this->sessionKey($user));
    }

    protected function sessionKey(User $user)
    {
        return md5(sprintf('teamstore-session-%s', (string) $user->getKey()));
    }
}
