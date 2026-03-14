<?php

namespace Modules\Todos\Actions\Board\Task\Comment;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TaskComment;

class CreatesTaskComment
{
    public function create($user, Task $task, array $params)
    {
        Gate::forUser($user)->authorize('createComment', $task);

        $validated = Validator::make($params, [
            'message' => 'required|string|min:3',
        ])->validated();

        $author = $task->board->getSubscription($user);

        $newComment = tap(new TaskComment($validated), fn ($newComment) => $newComment->author()->associate($author));

        $task->comments()->save($newComment);

        return $newComment;
    }
}
