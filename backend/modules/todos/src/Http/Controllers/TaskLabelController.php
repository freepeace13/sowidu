<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Modules\Todos\Actions\Board\Task\Label\AddTaskLabel;
use Modules\Todos\Actions\Board\Task\Label\RemoveTaskLabel;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Task;

class TaskLabelController extends InertiaController
{
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Board $board, Task $task)
    {
        (new AddTaskLabel)->add($request->user(), $task, $request->all());

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Board $board, Task $task, $id)
    {
        (new RemoveTaskLabel)->remove($this->user(), $task, $id);

        return back(303);
    }
}
