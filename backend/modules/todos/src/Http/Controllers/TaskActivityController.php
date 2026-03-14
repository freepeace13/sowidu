<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Task;
use Modules\Todos\Transformers\ActivityTransformer;

class TaskActivityController extends Controller
{
    public function __invoke(Request $request, Board $board, Task $task)
    {
        return response()->json(
            $task->activities()
                ->when(
                    $request->query('commentsOnly', false),
                    fn ($q) => $q->whereEvent('comment.created'),
                )
                ->latest()
                ->simplePaginate(10)
                ->withQueryString()
                ->through(function ($activity) {
                    return (new ActivityTransformer($activity))->withComment()->resolve();
                }),
        );
    }
}
