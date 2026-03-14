<?php

namespace Modules\Todos\Actions\Board\Task\Comment;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TaskComment;

class UpdatesTaskComment
{
    public function update($user, Task $task, TaskComment $comment, array $params)
    {
        Gate::forUser($user)->authorize('updateComment', [$task, $comment]);

        $validated = Validator::make($params, [
            'message' => 'required|string|min:3',
        ])->validated();

        $comment->fill($validated)->save();
    }
}
