<?php

namespace App\Http\Api\Controllers\V1\Todo;

use App\Actions\Todo\Board\Task\Comment\CreatesTaskComment;
use App\Http\Api\Resources\V1\Todo\TaskCommentResource;
use App\Models\Board;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Packages\RestApi\RestfulController;

class TaskCommentController extends RestfulController
{
    public function index(Request $request, Board $board, Task $task)
    {
        Gate::authorize('view', $board);

        abort_unless($task->board->is($board), 404, 'Task not found.');

        $comments = $task->comments()->latest()->paginate(10);

        return TaskCommentResource::collection($comments);
    }

    public function store(Request $request, Board $board, Task $task)
    {
        Gate::authorize('view', $board);

        abort_unless($task->board->is($board), 404, 'Task not found.');

        $comment = app(CreatesTaskComment::class)->create($request->user(), $task, [
            'message' => $request->message,
        ]);

        return new TaskCommentResource($comment);
    }

    public function destroy(Request $request, Board $board, Task $task)
    {
        //
    }
}
