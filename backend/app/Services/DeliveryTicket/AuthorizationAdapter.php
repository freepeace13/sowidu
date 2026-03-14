<?php

namespace App\Services\DeliveryTicket;

use App\Policies\Traits\HandlesTeamAuthorization;
use Illuminate\Support\Facades\Gate;
use Modules\DeliveryTicket\Contracts\External\AuthorizationContract;

class AuthorizationAdapter implements AuthorizationContract
{
    use HandlesTeamAuthorization;

    public function hasCompanyAccess(mixed $user, mixed $company): bool
    {
        return $this->teamAuthorize($user, $company);
    }

    public function can(mixed $user, string $ability, mixed $model): bool
    {
        return Gate::forUser($user)->allows($ability, $model);
    }

    public function getCurrentCompany(mixed $user): mixed
    {
        return $user->currentCompany ?? null;
    }
}
