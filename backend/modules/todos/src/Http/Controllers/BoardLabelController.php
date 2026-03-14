<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Modules\Todos\Actions\Board\Label\CreateBoardLabel;
use Modules\Todos\Actions\Board\Label\DeleteBoardLabel;
use Modules\Todos\Actions\Board\Label\UpdateBoardLabel;
use Modules\Todos\Models\Board;

class BoardLabelController extends InertiaController
{
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Board $board)
    {
        (new CreateBoardLabel)->create($request->user(), $board, $request->all());

        return back(303);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Board $board, $id)
    {
        (new UpdateBoardLabel)->update($request->user(), $board, $id, $request->all());

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Board $board, $id)
    {
        (new DeleteBoardLabel)->delete($this->user(), $board, $id);

        return back(303);
    }
}
