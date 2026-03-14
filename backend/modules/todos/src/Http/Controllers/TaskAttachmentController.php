<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Modules\Todos\Actions\Board\Task\Attachment\CreateTaskAttachment;
use Modules\Todos\Actions\Board\Task\Attachment\DeleteTaskAttachment;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TaskAttachment;

class TaskAttachmentController extends InertiaController
{
    public function store(Request $request, Board $board, Task $task)
    {
        (new CreateTaskAttachment)->create(
            $request->user(),
            $task,
            $request->all(),
        );

        return back(303);
    }

    public function destroy(Request $request, Board $board, Task $task, TaskAttachment $attachment)
    {
        (new DeleteTaskAttachment)->delete(
            $request->user(),
            $task,
            $attachment,
        );

        return back(303);
    }
}
