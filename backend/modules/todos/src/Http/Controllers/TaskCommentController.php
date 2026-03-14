<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Todos\Actions\Board\Task\Comment\CreatesTaskComment;
use Modules\Todos\Actions\Board\Task\Comment\DeleteTaskComment;
use Modules\Todos\Actions\Board\Task\Comment\UpdatesTaskComment;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TaskComment;

class TaskCommentController extends InertiaController
{
    public function store(Request $request, Board $board, Task $task): RedirectResponse
    {
        (new CreatesTaskComment)->create($request->user(), $task, $request->all());

        return back(303);
    }

    public function update(Request $request, Board $board, Task $task, TaskComment $comment): RedirectResponse
    {
        (new UpdatesTaskComment)->update($request->user(), $task, $comment, $request->all());

        return back(303);
    }

    public function destroy(Board $board, Task $task, TaskComment $comment): RedirectResponse
    {
        (new DeleteTaskComment)->delete($this->user(), $task, $comment);

        return back(303);
    }
}
