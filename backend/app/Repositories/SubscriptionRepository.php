<?php

namespace App\Repositories;

use Illuminate\Contracts\Auth\Authenticatable;

class SubscriptionRepository
{
    public function allow(Authenticatable $user, string $module)
    {
        $guard = $user->guardName();
        $package = $this->getPackage($this->getPackageName($guard));

        return in_array($module, $package);
    }

    protected function getPackageName($guard)
    {
        return config("subscription.guards.{$guard}");
    }

    protected function getPackage($name)
    {
        return config("subscription.packages.{$name}");
    }
}
