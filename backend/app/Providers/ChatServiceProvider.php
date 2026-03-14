<?php

namespace App\Providers;

use App\Services\Chat\AuthorizationAdapter;
use App\Services\Chat\BroadcasterAdapter;
use App\Services\Chat\MediaManagerAdapter;
use App\Services\Chat\UrnResolverAdapter;
use App\Services\Chat\UserDisplayAdapter;
use App\Services\Chat\UserSearchAdapter;
use Illuminate\Support\ServiceProvider;
use Modules\Chatly\Contracts\External\AuthorizationContract;
use Modules\Chatly\Contracts\External\BroadcasterContract;
use Modules\Chatly\Contracts\External\MediaManagerContract;
use Modules\Chatly\Contracts\External\UrnResolverContract;
use Modules\Chatly\Contracts\External\UserDisplayContract;
use Modules\Chatly\Contracts\External\UserSearchContract;

/**
 * Service Provider for Chatly module external dependencies.
 *
 * This provider binds all external contracts (outgoing ports) to their
 * concrete adapter implementations, following the Ports and Adapters
 * (Hexagonal Architecture) pattern.
 */
class ChatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind user search and participant resolution
        $this->app->bind(
            UserSearchContract::class,
            UserSearchAdapter::class,
        );

        // Bind URN identifier resolution
        $this->app->bind(
            UrnResolverContract::class,
            UrnResolverAdapter::class,
        );

        // Bind media/file management
        $this->app->bind(
            MediaManagerContract::class,
            MediaManagerAdapter::class,
        );

        // Bind authorization and permissions
        $this->app->bind(
            AuthorizationContract::class,
            AuthorizationAdapter::class,
        );

        // Bind real-time broadcasting
        $this->app->bind(
            BroadcasterContract::class,
            BroadcasterAdapter::class,
        );

        // Bind user display information
        $this->app->bind(
            UserDisplayContract::class,
            UserDisplayAdapter::class,
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
