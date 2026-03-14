<?php

namespace Modules\Todos\Actions\Board\Task\TimeLog;

use Illuminate\Support\Facades\Gate;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TaskTimeLog;

class DeleteTaskTimeLog
{
    public function delete($user, Task $task, TaskTimeLog $taskTimeLog)
    {
        Gate::forUser($user)->authorize('destroyTimeLog', [$task, $taskTimeLog]);

        $taskTimeLog->delete();
    }
}
