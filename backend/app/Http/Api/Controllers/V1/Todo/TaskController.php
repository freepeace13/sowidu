<?php

namespace App\Http\Api\Controllers\V1\Todo;

use App\Actions\Todo\Board\Task\CreatesBoardTask;
use App\Actions\Todo\Board\Task\UpdatesBoardTask;
use App\Http\Api\Resources\V1\Todo\TaskResource;
use App\Models\Board;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Packages\RestApi\RestfulController;

class TaskController extends RestfulController
{
    // TODO: Use eloquent filter
    public function index(Request $request, Board $board)
    {
        Gate::authorize('viewTasks', $board);

        $tasks = $board->tasks();

        if ($group = $request->query('group')) {
            $tasks->whereGroup($group);
        }

        return TaskResource::collection($tasks->latest()->get());
    }

    public function show(Request $request, Board $board, Task $task)
    {
        abort_unless($task->board->is($board), 404, 'Task not found.');

        Gate::authorize('view', $task);

        return (new TaskResource($task))
            ->withMembers()
            ->withReporter();
    }

    public function store(Request $request, Board $board)
    {
        $newTask = app(CreatesBoardTask::class)->create(
            $request->user(),
            $board,
            $request->all(),
        );

        return new TaskResource($newTask);
    }

    public function update(Request $request, Board $board, Task $task)
    {
        abort_unless($task->board->is($board), 404, 'Task not found.');

        $updatedTask = app(UpdatesBoardTask::class)->update(
            $request->user(),
            $task,
            $request->all(),
        );

        return (new TaskResource($updatedTask))
            ->withMembers()
            ->withReporter();
    }
}
