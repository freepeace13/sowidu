<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Todos\Models\Board;
use Modules\Todos\Transformers\ActivityTransformer;

class BoardActivityController extends Controller
{
    public function __invoke(Request $request, Board $board)
    {
        return response()->json(
            $board->activities()
                ->with(['causer.profile.avatar'])
                ->latest()
                ->simplePaginate(10)
                ->withQueryString()
                ->through(function ($activity) {
                    return (new ActivityTransformer($activity))->withComment()->resolve();
                }),
        );
    }
}
