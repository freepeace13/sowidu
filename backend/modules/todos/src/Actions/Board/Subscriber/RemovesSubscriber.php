<?php

namespace Modules\Todos\Actions\Board\Subscriber;

use Illuminate\Support\Facades\Gate;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Subscriber;

class RemovesSubscriber
{
    public function remove($user, Board $board, Subscriber $subscriber)
    {
        Gate::forUser($user)->authorize('removeSubscriber', $board);

        // Ensure subscriber is not owner of the board
        throw_validation_if(
            $board->hasAnyRole($subscriber->user, ['owner', 'co-owner']),
            'This user is the owner of this board.',
        );

        // Ensure subscriber has subscribed to the board
        throw_validation_unless(
            $board->hasUser($subscriber->user),
            'This user is not subscribed to this board.',
        );

        // Detach subscriber tasks
        $board->tasks()
            ->with('members')
            ->whereHas('members', fn ($q) => $q->where('subscriber_id', $subscriber->id))
            ->each(fn ($task) => $task->members()->detach($subscriber));

        // Remove subscriber from the board
        $board->users()->detach($subscriber->user);
    }
}
