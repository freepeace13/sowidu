<?php

namespace Modules\Todos\Actions\Board\Task\Comment;

use Illuminate\Support\Facades\Gate;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TaskComment;

class DeleteTaskComment
{
    public function delete($user, Task $task, TaskComment $comment)
    {
        Gate::forUser($user)->authorize('destroyComment', [$task, $comment]);

        $comment->delete();
    }
}
