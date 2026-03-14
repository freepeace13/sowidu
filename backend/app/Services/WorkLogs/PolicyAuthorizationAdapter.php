<?php

declare(strict_types=1);

namespace App\Services\WorkLogs;

use App\Traits\InteractsWithImpersonator;
use Modules\WorkLogs\Contracts\External\PolicyAuthorizationContract;

class PolicyAuthorizationAdapter implements PolicyAuthorizationContract
{
    use InteractsWithImpersonator {
        isImpersonating as traitIsImpersonating;
        getCurrentTeamId as traitGetCurrentTeamId;
        getCurrentEmployee as traitGetCurrentEmployee;
    }

    public function isImpersonating(): bool
    {
        return $this->traitIsImpersonating();
    }

    public function getCurrentTeamId(): ?int
    {
        return $this->traitGetCurrentTeamId();
    }

    public function getCurrentEmployee(): mixed
    {
        return $this->traitGetCurrentEmployee();
    }

    public function canRepresentTeam(mixed $user, ?int $teamId, string $permission): bool
    {
        $employee = $this->getCurrentEmployee();

        return $this->ensureUserBelongsToTeam($user, $teamId) &&
            $this->canManageForTeam($employee, $permission);
    }

    public function isAuthorizedTo(mixed $user, string $permission, ?int $teamId = null): bool
    {
        $teamId = $teamId ?? $this->getCurrentTeamId();

        if (!$teamId) {
            return false;
        }

        return $this->canRepresentTeam($user, $teamId, $permission);
    }

    protected function ensureUserBelongsToTeam(mixed $user, ?int $teamId): bool
    {
        if ($teamId === null) {
            return false;
        }

        return $user->loadMissing(['teams'])
            ->teams
            ->contains(fn ($team) => $team->id == $teamId);
    }

    protected function canManageForTeam(mixed $employee, string $permission): bool
    {
        return $employee->can($permission);
    }
}
