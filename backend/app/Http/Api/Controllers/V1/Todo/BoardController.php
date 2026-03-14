<?php

namespace App\Http\Api\Controllers\V1\Todo;

use App\Actions\Todo\Board\CreatesBoard;
use App\Http\Api\Resources\V1\Todo\BoardResource;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Packages\RestApi\RestfulController;

class BoardController extends RestfulController
{
    public function index(Request $request)
    {
        $user = $request->user();

        $boards = Board::query()
            ->where('team_id', $this->getCurrentTeamId())
            ->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->getKey());
            })
            ->get();

        return BoardResource::collection($boards);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $board = app(CreatesBoard::class)->create($user, [
            'title' => $request->title,
            'description' => $request->description,
            'team_id' => $this->getCurrentTeamId(),
        ]);

        return (new BoardResource($board))
            ->withSubscribers();
    }

    public function show(Board $board)
    {
        Gate::authorize('view', $board);

        abort_unless($board->team_id === $this->getCurrentTeamId(), 404, 'Not found');

        return (new BoardResource($board))
            ->withSubscribers();
    }
}
