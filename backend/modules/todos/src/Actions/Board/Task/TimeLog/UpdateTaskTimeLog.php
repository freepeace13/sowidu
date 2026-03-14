<?php

namespace Modules\Todos\Actions\Board\Task\TimeLog;

use Illuminate\Support\Facades\Gate;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TaskTimeLog;

class UpdateTaskTimeLog extends TaskTimeLogBase
{
    public function update($user, Task $task, TaskTimeLog $taskTimeLog, array $params): TaskTimeLog
    {
        Gate::forUser($user)->authorize('destroyTimeLog', [$task, $taskTimeLog]);

        $validated = $this->validate($params);

        $taskTimeLog->fill($validated)->save();

        return $taskTimeLog;
    }
}
