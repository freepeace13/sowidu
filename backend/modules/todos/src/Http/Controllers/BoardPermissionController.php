<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Todos\Actions\Board\UpdateBoardPermissions;
use Modules\Todos\Models\Board;

class BoardPermissionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Board $board)
    {
        (new UpdateBoardPermissions)->update($request->user(), $board, $request->all());

        return back(303);
    }
}
