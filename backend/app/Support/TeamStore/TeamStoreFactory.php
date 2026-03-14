<?php

namespace App\Support\TeamStore;

use InvalidArgumentException;

class TeamStoreFactory
{
    public static function create($guard = null): TeamStore
    {
        $guard = $guard ?: config('auth.defaults.guard');

        return match ($guard) {
            'web' => new WebGuardStore,
            'sanctum' => new SanctumGuardStore,
            default => throw new InvalidArgumentException(sprintf('Unsupported team store for guard [%s]', $guard)),
        };
    }
}
