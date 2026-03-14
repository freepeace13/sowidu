<?php

namespace Modules\Todos\Actions\Board\Task\Label;

use Illuminate\Support\Facades\Gate;
use Modules\Todos\Models\Task;

class RemoveTaskLabel
{
    public function remove($user, Task $task, int $labelId)
    {
        Gate::forUser($user)->authorize('removeLabel', $task);

        $this->validate($task, $labelId);

        $task->removeLabel($labelId);
    }

    protected function validate(Task $task, int $labelId)
    {
        $label = $task->board->settings()->labels()->find($labelId);
        abort_if(!$label, 404, 'Label not found.');

        abort_if(
            !$task->labels()->where('label_id', $labelId)->exists(),
            404,
            'Label not found on this task.',
        );
    }
}
