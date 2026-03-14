<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Todos\Actions\Board\Task\TimeLog\AddTaskTimeLog;
use Modules\Todos\Actions\Board\Task\TimeLog\DeleteTaskTimeLog;
use Modules\Todos\Actions\Board\Task\TimeLog\UpdateTaskTimeLog;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TaskTimeLog;
use Modules\Todos\Support\TodoHelper;
use Modules\Todos\Transformers\TaskTimeLogTransformer;

class TaskTimeLogController extends Controller
{
    public function index(Request $request, Board $board, Task $task)
    {
        $totalHours = $task->timeLogsTotalDuration();

        return response()->json([
            'data' => $task->timeLogs()
                ->filters($request->only(['sortBy', 'descending', 'groupByUser']))
                ->with(['author.user.profile.avatar', 'task'])
                ->paginate($request->get('rowsPerPage', 10))
                ->withQueryString()
                ->through(function ($timeLog) use ($request) {
                    return (new TaskTimeLogTransformer($timeLog))->withUser()->withIsOwnedFlag($request->user())->resolve();
                }),
            'total_hours' => [
                'text' => TodoHelper::durationForHumans($totalHours),
                'original' => $totalHours,
            ],
        ]);
    }

    public function store(Request $request, Board $board, Task $task)
    {
        (new AddTaskTimeLog)->add($request->user(), $task, $request->all());

        return back(303);
    }

    public function update(Request $request, Board $board, Task $task, TaskTimeLog $time_log)
    {
        (new UpdateTaskTimeLog)->update($request->user(), $task, $time_log, $request->all());

        return back(303);
    }

    public function destroy(Request $request, Board $board, Task $task, TaskTimeLog $time_log)
    {
        (new DeleteTaskTimeLog)->delete($request->user(), $task, $time_log);

        return back(303);
    }
}
