<?php

namespace Modules\Todos\Actions\Board\Task\Label;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Todos\Models\Task;

class AddTaskLabel
{
    public function add($user, Task $task, array $params)
    {
        Gate::forUser($user)->authorize('addLabel', $task);

        $validated = $this->validate($task, $params);

        return $task->addLabel($validated['label']);
    }

    protected function validate($task, $params)
    {
        $labelIds = $task->board->settings()->labels()->all()->pluck('id');
        $existingLabelIds = $task->labels->pluck('label_id');

        return Validator::make($params, [
            'label' => [
                'required',
                'in:' . $labelIds->join(','),
                'not_in:' . $existingLabelIds->join(','),
            ],
        ])->validateWithBag('addTaskLabel');
    }
}
