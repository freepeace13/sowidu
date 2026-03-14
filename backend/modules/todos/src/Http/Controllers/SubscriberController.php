<?php

namespace Modules\Todos\Http\Controllers;

use App\Http\Controllers\Inertia\InertiaController;
use Illuminate\Http\Request;
use Modules\Todos\Actions\Board\Subscriber\AddsSubscriber;
use Modules\Todos\Actions\Board\Subscriber\RemovesSubscriber;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Subscriber;
use Modules\Todos\Transformers\SubscriberTransformer;

class SubscriberController extends InertiaController
{
    public function index(Request $request, Board $board)
    {
        return response()->json([
            'results' => $board->subscribers()
                ->whereHas('user', fn ($q) => $q->search($request->keyword))
                ->get()
                ->map(fn ($subsriber) => (new SubscriberTransformer($subsriber))->withUser()->resolve()),
        ]);
    }

    public function store(Request $request, Board $board)
    {
        app(AddsSubscriber::class)->add($request->user(), $board, [
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return back(303);
    }

    public function destroy(Request $request, Board $board, Subscriber $subscriber)
    {
        app(RemovesSubscriber::class)->remove(
            $request->user(),
            $board,
            $subscriber,
        );

        return back(303);
    }
}
