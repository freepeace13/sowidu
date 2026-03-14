<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Modules\Todos\Actions\Board\Manual\CreateManualTask;
use Modules\Todos\Models\Board;

class ManualTaskController extends InertiaController
{
    public function store(Request $request, Board $board)
    {
        app(CreateManualTask::class)->create(
            $request->user(),
            $board,
            $request->all(),
        );

        return back(303);
    }
}
