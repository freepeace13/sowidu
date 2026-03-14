<?php

namespace App\Http\Api\Controllers\V1\Todo;

use App\Actions\Todo\Board\Task\Member\AddsTaskMember;
use App\Actions\Todo\Board\Task\Member\RemovesTaskMember;
use App\Models\Board;
use App\Models\Subscriber;
use App\Models\Task;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class TaskMemberController extends RestfulController
{
    public function store(Request $request, Board $board, Task $task)
    {
        abort_unless($task->board->is($board), 404, 'Task not found.');

        app(AddsTaskMember::class)->add($request->user(), $task, [
            'subscriber_id' => $request->subscriberId,
        ]);

        return $this->respondOk();
    }

    public function destroy(Request $request, Board $board, Task $task, Subscriber $subscriber)
    {
        abort_unless($task->board->is($board), 404, 'Task not found.');

        app(RemovesTaskMember::class)->remove(
            $request->user(),
            $task,
            $subscriber,
        );

        return $this->respondOk();
    }
}
