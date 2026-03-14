<?php

namespace Modules\Todos\Actions\Board\Task;

use Illuminate\Support\Facades\Gate;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Task;

class DuplicateBoardTask
{
    public function duplicate($user, Board $board, Task $task)
    {
        Gate::forUser($user)->authorize('duplicateTask', $board);

        $reporter = $board->getSubscription($user);

        $newTask = $task->replicate(['reporter_id'])->reporter()->associate($reporter);

        return $board->tasks()->save($newTask);
    }
}
