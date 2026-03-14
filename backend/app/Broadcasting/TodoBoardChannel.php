<?php

namespace App\Broadcasting;

use App\Models\Board;
use Illuminate\Contracts\Auth\Access\Authorizable;

class TodoBoardChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @return array|bool
     */
    public function join(Authorizable $user, $boardId)
    {
        $board = Board::findOrFail($boardId);

        return $board->hasUser($user);
    }
}
