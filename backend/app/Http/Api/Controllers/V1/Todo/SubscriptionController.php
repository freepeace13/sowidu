<?php

namespace App\Http\Api\Controllers\V1\Todo;

use App\Actions\Todo\Board\Subscriber\AddsSubscriber;
use App\Actions\Todo\Board\Subscriber\RemovesSubscriber;
use App\Http\Api\Resources\V1\Todo\SubscriberResource;
use App\Models\Board;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Packages\RestApi\RestfulController;

class SubscriptionController extends RestfulController
{
    public function index(Request $request, Board $board)
    {
        Gate::authorize('view', $board);

        // Should also be searchable by keyword
        $subscribers = $board->subscribers()
            ->whereNotIn('id', $request->query('except', []))
            ->get();

        return SubscriberResource::collection($subscribers);
    }

    public function store(Request $request, Board $board)
    {
        app(AddsSubscriber::class)->add($request->user(), $board, [
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return $this->respondOk();
    }

    public function update(Request $request, Board $board, Subscriber $subscriber)
    {
        abort_unless($board->is($subscriber->board), 404, 'Subscriber not found.');

        // Update the subscriber details

        return new SubscriberResource($subscriber);
    }

    public function destroy(Request $request, Board $board, Subscriber $subscriber)
    {
        abort_unless($board->is($subscriber->board), 404, 'Subscriber not found.');

        app(RemovesSubscriber::class)->remove(
            $request->user(),
            $board,
            $subscriber,
        );

        return $this->respondOk();
    }
}
