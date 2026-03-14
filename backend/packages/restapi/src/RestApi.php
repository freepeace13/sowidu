<?php

namespace Packages\RestApi;

use Illuminate\Contracts\Foundation\Application;

// @TODO - Will create single decorator for both session (web) and tokenize (api) driver.
class RestApi
{
    /**
     * @var Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * @var Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    protected static $features = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->auth = $app->make('auth');
    }

    public static function isApiRequest($request)
    {
        if (!$request->header('X-Inertia')) {
            /** @todo add more request checking. Add custom headers or something. */

            return $request->wantsJson() || $request->expectsJson();
        }

        return false;
    }

    public function user()
    {
        return $this->auth()->user();
    }

    public function team()
    {
        $user = $this->user();

        if ($team = $user->currentTeam()) {
            $team->membership = $user->teamMembership($team);
            $team->roles = $user->teamRoles($team);
            $team->permissions = $user->teamPermissions($team);
        }

        return $team;
    }

    protected function auth()
    {
        return $this->auth->guard();
    }

    public function response($data = null, $status = 200, array $headers = [])
    {
        return response()->json($data, $status, array_merge([
            'X-Api-Version' => 'v1', // @TODO - Will finalize api response headers needed
        ], $headers));
    }

    public static function configure($app)
    {
        $app->singleton(RestApi::class, function ($app) {
            return new static($app);
        });
    }
}
