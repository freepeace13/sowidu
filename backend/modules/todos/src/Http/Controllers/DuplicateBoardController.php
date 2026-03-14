<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Modules\Todos\Actions\Board\DuplicateBoard;
use Modules\Todos\Models\Board;

class DuplicateBoardController extends InertiaController
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Board $board)
    {
        app(DuplicateBoard::class)->duplicate($request->user(), $board);

        return back(303);
    }
}
