<?php

declare(strict_types=1);

namespace App\Services\WorkLogs;

use App\Traits\InteractsWithImpersonator;
use Illuminate\Support\Facades\Gate;
use Modules\WorkLogs\Contracts\External\AuthorizationContract;

class AuthorizationAdapter implements AuthorizationContract
{
    use InteractsWithImpersonator;

    public function can(mixed $user, string $ability, mixed $resource = null): bool
    {
        if ($resource === null) {
            return Gate::forUser($user)->allows($ability);
        }

        return Gate::forUser($user)->allows($ability, $resource);
    }

    public function authorize(mixed $user, string $ability, mixed $resource = null): void
    {
        if ($resource === null) {
            Gate::forUser($user)->authorize($ability);

            return;
        }

        Gate::forUser($user)->authorize($ability, $resource);
    }

    public function hasPermission(mixed $user, string $permission): bool
    {
        if (method_exists($user, 'hasPermissionTo')) {
            return $user->hasPermissionTo($permission);
        }

        if (method_exists($user, 'permissions')) {
            return $user->permissions->contains('name', $permission);
        }

        return false;
    }

    public function isImpersonating(): bool
    {
        return $this->checkIsImpersonating();
    }

    public function getCurrentTeamId(): ?int
    {
        return $this->getImpersonatingTeamId();
    }

    private function checkIsImpersonating(): bool
    {
        return session()->has('impersonate_user_id');
    }

    private function getImpersonatingTeamId(): ?int
    {
        return session('impersonate_company_id');
    }
}
