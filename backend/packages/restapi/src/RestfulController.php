<?php

namespace Packages\RestApi;

use Illuminate\Routing\Controller;

class RestfulController extends Controller
{
    /**
     * @return \App\Models\User|null
     */
    public function currentUser()
    {
        return $this->api()
            ->user();
    }

    /**
     * @return \App\Models\Company|null
     */
    public function currentTeam()
    {
        return $this->api()
            ->team();
    }

    public function response(...$parameters)
    {
        return $this->api()
            ->response(...$parameters);
    }

    protected function api()
    {
        return app(RestApi::class);
    }

    protected function currentUserIsEmployee(): bool
    {
        return filled($this->currentTeam());
    }

    protected function currentUserIsPrivate(): bool
    {
        return blank($this->currentTeam());
    }
}
