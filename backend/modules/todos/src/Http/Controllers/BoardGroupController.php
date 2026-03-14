<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Modules\Todos\Actions\Board\Group\CreatesBoardGroup;
use Modules\Todos\Actions\Board\Group\DeletesBoardGroup;
use Modules\Todos\Actions\Board\Group\UpdateBoardGroup;
use Modules\Todos\Models\Board;

class BoardGroupController extends InertiaController
{
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Board $board)
    {
        (new CreatesBoardGroup)->create($request->user(), $board, $request->all());

        return back(303);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Board $board, $group)
    {
        (new UpdateBoardGroup)->update($request->user(), $board, $group, $request->all());

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Board $board, $group)
    {
        (new DeletesBoardGroup)->delete($request->user(), $board, $group);

        return back(303);
    }
}
