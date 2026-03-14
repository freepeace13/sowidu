<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Todos\Actions\Board\CreatesBoard;
use Modules\Todos\Actions\Board\DeleteBoard;
use Modules\Todos\Actions\Board\GetBoard;
use Modules\Todos\Actions\Board\UpdateBoard;
use Modules\Todos\Models\Board;
use Modules\Todos\Transformers\BoardTransformer;

class BoardController extends InertiaController
{
    public function index(Request $request)
    {
        $boards = (new GetBoard)->get($request->user(), array_merge([
            'team_id' => $this->getCurrentTeamId(),
        ], $request->only('q')));

        return Inertia::render('Index', [
            'boards' => $boards->map(fn ($board) => (new BoardTransformer($board))->withOwner()->resolve()),
            'filters' => $request->only('q'),
        ]);
    }

    public function store(Request $request)
    {
        (new CreatesBoard)->create($request->user(), [
            'title' => $request->title,
            'description' => $request->description,
            'team_id' => $this->getCurrentTeamId(),
            'logo' => $request->hasFile('logo') ? $request->logo : null,
        ]);

        return back(303);
    }

    public function show(Board $board)
    {
        $this->authorize('view', $board);

        return Inertia::render('Show', [
            'board' => (new BoardTransformer($board))
                ->withSubscribers()
                ->resolve(),
        ]);
    }

    public function showTaskGroup(Board $board)
    {
        $this->authorize('view', $board);

        return Inertia::render('TaskGroup', [
            'board' => (new BoardTransformer($board))
                ->withSubscribers()
                ->resolve(),
        ]);
    }

    public function update(Request $request, Board $board)
    {
        (new UpdateBoard)->update($request->user(), $board, $request->all());

        return back(303);
    }

    public function destroy(Request $request, Board $board)
    {
        (new DeleteBoard)->destroy($request->user(), $board);

        return back(303);
    }
}
