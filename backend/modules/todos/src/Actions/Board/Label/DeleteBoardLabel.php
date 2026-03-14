<?php

namespace Modules\Todos\Actions\Board\Label;

use Illuminate\Support\Facades\Gate;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Task;

class DeleteBoardLabel
{
    public function delete($user, Board $board, int $labelId)
    {
        Gate::forUser($user)->authorize('deleteLabel', $board);

        $boardLabel = $board->settings()->labels();

        throw_validation_unless($label = $boardLabel->has($labelId), 'Board label does not exist.');

        throw_validation_if($label['isDefault'], 'This label is default from this board, you cannot delete it.');

        $boardLabel->remove($labelId);

        $board->tasks->map(fn (Task $task) => $task->removeLabel($labelId));
    }
}
