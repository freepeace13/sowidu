<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Policies\Concerns;

use Modules\WorkLogs\Contracts\External\PolicyAuthorizationContract;

/**
 * Trait for handling policy authorization.
 *
 * Uses PolicyAuthorizationContract to delegate authorization
 * without importing main app traits directly.
 */
trait HandlesPolicyAuthorization
{
    protected function getPolicyAuthorization(): PolicyAuthorizationContract
    {
        return app(PolicyAuthorizationContract::class);
    }

    protected function isImpersonating(): bool
    {
        return $this->getPolicyAuthorization()->isImpersonating();
    }

    protected function getCurrentTeamId(): ?int
    {
        return $this->getPolicyAuthorization()->getCurrentTeamId();
    }

    protected function getCurrentEmployee(): mixed
    {
        return $this->getPolicyAuthorization()->getCurrentEmployee();
    }

    protected function canRepresentTeam(mixed $user, ?int $teamId, string $permission): bool
    {
        return $this->getPolicyAuthorization()->canRepresentTeam($user, $teamId, $permission);
    }

    protected function isAuthorizedTo(mixed $user, string $permission, ?int $teamId = null): bool
    {
        return $this->getPolicyAuthorization()->isAuthorizedTo($user, $permission, $teamId);
    }
}
