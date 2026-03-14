<?php

namespace Modules\Todos\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Todos\Models\Board;
use Modules\Todos\Traits\ChecksTodoPolicies;

class BoardPolicy
{
    use ChecksTodoPolicies, HandlesAuthorization;

    public function update(User $user, Board $board)
    {
        if ($board->forTeam() && !$board->hasAnyRole($user, ['owner', 'co-owner'])) {
            return Response::deny('Only owner and co-owner can update this board.');
        }

        return $board->hasAnyRole($user, ['owner', 'co-owner']);
    }

    public function delete(User $user, Board $board)
    {
        if ($board->isPredefined()) {
            return Response::deny('Cannot delete pre-defined board.');
        }

        return $board->hasAnyRole($user, ['owner', 'co-owner']);
    }

    public function view($user, Board $board)
    {
        if ($this->hasValidSubscription($board, $user)) {
            return true;
        }
    }

    public function createGroup($user, Board $board)
    {
        if ($board->hasAnyRole($user, ['owner', 'co-owner'])) {
            return true;
        }

        return $this->hasValidSubscription($board, $user) && $this->membersCanManageGroup($board);
    }

    public function updateGroup($user, Board $board)
    {
        if ($board->hasAnyRole($user, ['owner', 'co-owner'])) {
            return true;
        }

        return $this->hasValidSubscription($board, $user) && $this->membersCanManageGroup($board);
    }

    public function deleteGroup($user, Board $board)
    {
        if ($board->hasAnyRole($user, ['owner', 'co-owner'])) {
            return true;
        }

        return $this->hasValidSubscription($board, $user) && $this->membersCanManageGroup($board);
    }

    public function viewTasks($user, Board $board)
    {
        if ($this->hasValidSubscription($board, $user)) {
            return true;
        }
    }

    public function createTask($user, Board $board)
    {
        if ($this->isBoardOwner($board, $user)) {
            return true;
        }

        if ($this->hasValidSubscription($board, $user)) {
            return $this->membersCanManageTask($board);
        }
    }

    public function duplicateTask($user, Board $board)
    {
        if ($this->isBoardOwner($board, $user)) {
            return true;
        }

        if ($this->hasValidSubscription($board, $user)) {
            return $this->membersCanManageTask($board);
        }
    }

    public function addSubscriber($user, Board $board)
    {
        if ($board->hasAnyRole($user, ['owner', 'co-owner'])) {
            return true;
        }

        if ($this->hasValidSubscription($board, $user)) {
            return $this->membersCanManageBoardSubscribers($board);
        }
    }

    public function removeSubscriber($user, Board $board)
    {
        if ($board->hasAnyRole($user, ['owner', 'co-owner'])) {
            return true;
        }

        if ($this->hasValidSubscription($board, $user)) {
            return $this->membersCanManageBoardSubscribers($board);
        }
    }

    public function manageLabel($user, Board $board)
    {
        if ($board->hasAnyRole($user, ['owner', 'co-owner'])) {
            return true;
        }

        return $this->hasValidSubscription($board, $user) && $this->membersCanManageLabel($board);
    }

    public function deleteLabel($user, Board $board)
    {
        if ($board->hasAnyRole($user, ['owner', 'co-owner'])) {
            return true;
        }

        return $this->hasValidSubscription($board, $user) && $this->membersCanManageLabel($board);
    }

    public function updatePermission($user, Board $board)
    {
        if ($board->hasAnyRole($user, ['owner', 'co-owner'])) {
            return true;
        }

        deny('Only board admins can update permission settings.');
    }
}
