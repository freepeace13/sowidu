<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Modules\Todos\Actions\Board\Task\Member\AddsTaskMember;
use Modules\Todos\Actions\Board\Task\Member\RemovesTaskMember;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Subscriber;
use Modules\Todos\Models\Task;

class TaskMemberController extends InertiaController
{
    public function store(Request $request, Board $board, Task $task)
    {
        app(AddsTaskMember::class)->add($request->user(), $task, [
            'subscriber_id' => $request->subscriber_id,
        ]);

        return back(303);
    }

    public function destroy(Request $request, Board $board, Task $task, Subscriber $subscriber)
    {
        app(RemovesTaskMember::class)->remove(
            $request->user(),
            $task,
            $subscriber,
        );

        return back(303);
    }
}
