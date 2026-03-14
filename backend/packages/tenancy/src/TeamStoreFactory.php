<?php

namespace Packages\Tenancy;

use InvalidArgumentException;

class TeamStoreFactory
{
    /**
     * Create a TeamStore instance for the given guard.
     * Automatically uses TestGuardStore when running in testing environment.
     *
     * @param  string|null  $guard
     */
    public static function create($guard = null): TeamStore
    {
        // Use TestGuardStore in testing environment
        if (static::isTestingEnvironment()) {
            return new TestGuardStore;
        }

        $guard = $guard ?: config('auth.defaults.guard');

        return match ($guard) {
            'web' => new WebGuardStore,
            'sanctum' => new SanctumGuardStore,
            default => throw new InvalidArgumentException(sprintf('Unsupported team store for guard [%s]', $guard)),
        };
    }

    /**
     * Determine if the application is running in a testing environment.
     */
    protected static function isTestingEnvironment(): bool
    {
        if (function_exists('app')) {
            $app = app();

            return $app->environment('testing') ||
                   ($app->runningInConsole() && $app->runningUnitTests());
        }

        // Fallback: check environment variable
        return env('APP_ENV') === 'testing';
    }
}
