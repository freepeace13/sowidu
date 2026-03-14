<?php

namespace Modules\Todos\Actions\Board\Task;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Task;

class CreatesBoardTask
{
    public function create($user, Board $board, array $params)
    {
        $validated = $this->validate($board, $params);

        if (Arr::has($validated, 'id')) {
            return (new DuplicateBoardTask)->duplicate($user, $board, Task::findOrFail($validated['id']));
        }

        if (array_key_exists('task_id', $validated)) {
            Gate::forUser($user)->authorize('canCreateSubTask', Task::findOrFail($validated['task_id']));
        } else {
            Gate::forUser($user)->authorize('createTask', $board);
        }

        $reporter = $board->getSubscription($user);

        $newTask = tap(new Task($validated), function ($newTask) use ($reporter) {
            $newTask->reporter()->associate($reporter);
        });

        $board->tasks()->save($newTask);

        return $newTask;
    }

    protected function validate(Board $board, $params)
    {
        $groupNames = $board->settings()->groups()->all()->pluck('name');

        if (!array_key_exists('group', $params)) {
            $params['group'] = config('todo.board.settings.groups.fallback');
        }

        return Validator::make($params, [
            'title' => 'required',
            'task_id' => [
                'nullable',
                'exists:todo_tasks,id',
                function ($attribute, $value, $fail) use ($board) {
                    $parentTask = Task::with('board')->findOrFail($value);

                    // Parent task must be on the same board
                    if ($parentTask->board->isNot($board)) {
                        $fail('Wrong board, please refresh the page and try again.');
                    }

                    // Parent task cannot be a subtask
                    if ($parentTask->isSubTask()) {
                        $fail('Subtask cannot be a parent or should not have a subtask.');
                    }
                },
            ],
            'description' => 'nullable',
            'group' => ['required', 'in:' . $groupNames->join(',')],
            'id' => 'nullable|exists:todo_tasks',
        ])->validateWithBag('createBoardTask');
    }
}
