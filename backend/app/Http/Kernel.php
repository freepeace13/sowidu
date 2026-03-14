<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SetPreferredLocaleMiddleware::class,
            \App\Http\Middleware\HandleActiveStatusMiddleware::class,
            // \App\Http\Middleware\SetCurrentTeamId::class,
            // \App\Http\Middleware\LockScreenMiddleware::class,
            // \Packages\Translation\Middleware\LocalizationMiddleware::class,

            // Reminders middleware
            \App\Http\Middleware\Reminders\SetupAccountAddress::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Packages\RestApi\Middleware\ApiFeaturesMiddleware::class,
            // \App\Http\Middleware\SetCurrentTeamId::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'private_user' => \App\Http\Middleware\EnsurePrivateUser::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \App\Http\Middleware\RoleOrPermissionMiddleware::class,
        'valid_employee' => \App\Http\Middleware\EnsureValidEmployee::class,
        'client' => \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
        'verified' => \App\Http\Middleware\EnsureAccountActivated::class,
        'json' => \App\Http\Middleware\VerifyJsonRequest::class,
        'inertia' => \App\Http\Middleware\HandleInertiaRequests::class,
        'impersonating' => \App\Http\Middleware\ImpersonatingMiddleware::class,
        'guestOrAuth' => \App\Http\Middleware\GuestOrAuth::class,
    ];

    protected function bootstrappers()
    {
        return array_merge(
            [\Bugsnag\BugsnagLaravel\OomBootstrapper::class],
            parent::bootstrappers(),
        );
    }
}
