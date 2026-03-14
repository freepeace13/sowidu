<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;

class VerifyUser extends LoginUser
{
    public function verify(int $routeId, string $hash)
    {
        $user = User::findOrFail($routeId);

        if (!hash_equals((string) $routeId, (string) $user->getKey())) {
            throw new AuthorizationException;
        }

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return $this->login($user);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            return $this->login($user);
        }
    }
}
