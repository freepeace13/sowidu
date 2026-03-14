<?php

namespace Packages\Tenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session as SessionStore;

class WebGuardStore implements TeamStore
{
    public function setTeamId(Model $user, $teamId): void
    {
        SessionStore::put($this->sessionKey($user), $teamId);
    }

    public function getTeamId(Model $user): ?int
    {
        return SessionStore::get($this->sessionKey($user));
    }

    protected function sessionKey(Model $user)
    {
        return md5(sprintf('teamstore-session-%s', (string) $user->getKey()));
    }
}
