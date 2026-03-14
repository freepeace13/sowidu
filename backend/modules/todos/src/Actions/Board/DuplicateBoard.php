<?php

namespace Modules\Todos\Actions\Board;

use Modules\Todos\Models\Board;

class DuplicateBoard
{
    public function duplicate($user, Board $board)
    {
        return (new CreatesBoard)->create($user, $board->only([
            'team_id',
            'title',
            'description',
            'logo_path',
        ]));
    }
}
