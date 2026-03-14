<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Shared\Enums\Permissions;
use Modules\WorkLogs\Models\WorkLog;
use Modules\WorkLogs\Policies\Concerns\HandlesPolicyAuthorization;

class WorkLogPolicy
{
    use HandlesAuthorization;
    use HandlesPolicyAuthorization;

    public function before($user, $ability)
    {
        if (!$this->isImpersonating()) {
            return false;
        }
    }

    public function createForOtherEmployees($user)
    {
        return $this->canRepresentTeam(
            $user,
            $this->getCurrentTeamId(),
            Permissions::CAN_ADD_MANUAL_WORK_LOG_ENTRY_FOR_OTHERS,
        );
    }

    public function update($user, WorkLog $workLog)
    {
        // Check if user is the owner or has permissions
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_WORK_LOGS)
            || $workLog->author?->is($user);
    }

    public function delete($user, WorkLog $workLog)
    {
        // Check if user is the owner or has permissions
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_WORK_LOGS)
            || $workLog->author?->is($user);
    }

    public function viewOthersWorkLogs($user)
    {
        return $this->canRepresentTeam(
            $user,
            $this->getCurrentTeamId(),
            Permissions::CAN_VIEW_OTHERS_WORK_LOGS,
        );
    }
}
