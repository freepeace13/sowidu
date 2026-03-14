<?php

namespace Modules\Todos\Actions\Board\Task;

use Illuminate\Support\Facades\Gate;
use Modules\Todos\Models\Task;

class DeleteBoardTask
{
    public function delete($user, Task $task)
    {
        Gate::forUser($user)->authorize('delete', $task);

        return $task->delete();
    }
}
