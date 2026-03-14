<?php

namespace Modules\Todos\Actions\Board\Task\TimeLog;

use Illuminate\Support\Facades\Gate;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TaskTimeLog;

class AddTaskTimeLog extends TaskTimeLogBase
{
    public function add($user, Task $task, array $params): TaskTimeLog
    {
        Gate::forUser($user)->authorize('addTimeLog', $task);

        $validated = $this->validate($params);

        $author = $task->board->getSubscription($user);

        $timeLog = tap(new TaskTimeLog($validated), function ($timeLog) use ($task, $author) {
            $timeLog->author()->associate($author);
            $timeLog->board()->associate($task->board);
        });

        $task->timeLogs()->save($timeLog);

        return $timeLog;
    }
}
