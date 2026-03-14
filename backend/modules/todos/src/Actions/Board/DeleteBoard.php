<?php

namespace Modules\Todos\Actions\Board;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\Todos\Models\Board;

class DeleteBoard
{
    public function destroy(User $user, Board $board)
    {
        Gate::forUser($user)->authorize('delete', $board);

        return $board->delete();
    }
}
