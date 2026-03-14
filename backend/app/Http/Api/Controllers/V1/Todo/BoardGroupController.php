<?php

namespace App\Http\Api\Controllers\V1\Todo;

use App\Actions\Todo\Board\Group\CreatesBoardGroup;
use App\Actions\Todo\DeletesBoardGroup;
use App\Models\Board;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class BoardGroupController extends RestfulController
{
    public function store(Request $request, Board $board)
    {
        $creator = app(CreatesBoardGroup::class);

        $newGroup = $creator->create($request->user(), $board, [
            'name' => $request->name,
            'color' => $request->color,
        ]);

        return $this->responseJson($newGroup);
    }

    public function destroy(Request $request, Board $board)
    {
        app(DeletesBoardGroup::class)->delete(
            $request->user(),
            $board,
            $request->name,
        );

        return $this->respondOk();
    }
}
