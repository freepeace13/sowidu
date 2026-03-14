<?php

namespace Modules\Todos\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TaskAttachment;
use Modules\Todos\Models\TaskComment;
use Modules\Todos\Models\TaskTimeLog;
use Modules\Todos\Traits\ChecksTodoPolicies;

class TaskPolicy
{
    use ChecksTodoPolicies, HandlesAuthorization;

    public function view($user, Task $task)
    {
        if ($this->hasValidSubscription($task->board, $user)) {
            return true;
        }
    }

    public function canCreateSubTask($user, Task $task)
    {
        $task->loadMissing(['board']);

        if ($this->isBoardOwner($task->board, $user)) {
            return true;
        }

        if ($this->hasValidSubscription($task->board, $user)) {
            if ($task->hasMember($user)) {
                return true;
            }

            if ($task->isSubTask() && $task->parentTask->hasMember($user)) {
                return true;
            }

            return $this->membersCanManageTask($task->board);
        }
    }

    public function update($user, Task $task)
    {
        $task->loadMissing(['board']);

        if ($this->isBoardOwner($task->board, $user)) {
            return true;
        }

        if ($this->hasValidSubscription($task->board, $user)) {
            if ($task->hasMember($user)) {
                return true;
            }

            if ($task->isSubTask() && $task->parentTask->hasMember($user)) {
                return true;
            }

            return $this->membersCanManageTask($task->board);
        }
    }

    public function delete($user, Task $task)
    {
        if ($this->isBoardOwner($task->board, $user)) {
            return true;
        }

        if ($this->hasValidSubscription($task->board, $user)) {
            if ($task->hasMember($user)) {
                return true;
            }

            if ($task->isSubTask() && $task->parentTask->hasMember($user)) {
                return true;
            }

            return $this->membersCanManageTask($task->board);
        }
    }

    public function addMember($user, Task $task)
    {
        if ($this->hasValidSubscription($task->board, $user)) {
            return true;
        }
    }

    public function removeMember($user, Task $task)
    {
        if ($this->hasValidSubscription($task->board, $user)) {
            return true;
        }
    }

    public function addLabel($user, Task $task)
    {
        if ($this->isBoardOwner($task->board, $user)) {
            return true;
        }

        return $this->hasValidSubscription($task->board, $user);
    }

    public function removeLabel($user, Task $task)
    {
        return $this->hasValidSubscription($task->board, $user);
    }

    public function createComment($user, Task $task)
    {
        $task->loadMissing(['board']);

        if ($this->isBoardOwner($task->board, $user)) {
            return true;
        }

        $hasValidSubscription = $this->hasValidSubscription($task->board, $user);

        // Allow member to comment if user is a member of the task
        if ($hasValidSubscription && $task->hasMember($user)) {
            return true;
        }

        deny_unless(
            $allowMembersToComment = $task->board->settings()->permissions()->allow('members', 'can_comment'),
            'You cannot comment on a task if you are not assigned. Please contact the board admin to allow all members to comment.',
        );

        return $hasValidSubscription && $allowMembersToComment;
    }

    public function updateComment($user, Task $task, TaskComment $comment)
    {
        return $this->hasValidSubscription($task->board, $user) && $comment->isOwner($user);
    }

    public function destroyComment($user, Task $task, TaskComment $comment)
    {
        return $this->hasValidSubscription($task->board, $user) && $comment->isOwner($user);
    }

    public function addAttachment($user, Task $task)
    {
        return $this->hasValidSubscription($task->board, $user) || $user->is($task->board->owner());
    }

    public function destroyAttachment($user, Task $task, TaskAttachment $taskAttachment)
    {
        return $this->hasValidSubscription($task->board, $user) && $taskAttachment->isOwner($user);
    }

    public function addTimeLog($user, Task $task)
    {
        return $this->hasValidSubscription($task->board, $user) || $user->is($task->board->owner());
    }

    public function updateTimeLog($user, Task $task, TaskTimeLog $taskTimeLog)
    {
        return $this->hasValidSubscription($task->board, $user) && $taskTimeLog->isOwner($user);
    }

    public function destroyTimeLog($user, Task $task, TaskTimeLog $taskTimeLog)
    {
        return $this->hasValidSubscription($task->board, $user) && $taskTimeLog->isOwner($user);
    }
}
