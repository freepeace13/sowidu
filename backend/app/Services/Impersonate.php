<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class Impersonate
{
    protected $user;

    protected $sessionKey;

    protected $guard;

    public function __construct($user = null)
    {
        $this->user = $user;
        $this->guard = config('auth.defaults.guard');
    }

    public function impersonate($tenant)
    {
        if (is_int($tenant)) {
            $tenant = Company::find($tenant);
        }

        return $this->user()->switchTeam($tenant);
    }

    public function leave()
    {
        $this->user()->switchTeam(null);
    }

    /** @return bool */
    public function isImpersonating($tenant = null)
    {
        if ($tenant && $this->tenant()) {
            return $this->tenant()->is($tenant);
        }

        return $this->tenant() !== null;
    }

    public function canImpersonate($tenant)
    {
        return $this->impersonator($tenant) !== null;
    }

    /** @return Company|null */
    public function tenant()
    {
        return $this->user()?->currentTeam();
    }

    public function user()
    {
        if (!$this->user) {
            $this->user = Auth::guard($this->guard)->user();
        }

        return $this->user;
    }

    /** @return Company|\App\Models\User */
    public function account()
    {
        return $this->tenant() ?? $this->user();
    }

    public function as($user)
    {
        return new static($user);
    }

    public function impersonator($tenant = null)
    {
        if (!$tenant) {
            $tenant = $this->user()->currentTeam();
        }

        if (is_int($tenant)) {
            $tenant = Company::find($tenant);
        }

        return $this->user()->teamMembership($tenant);
    }
}
