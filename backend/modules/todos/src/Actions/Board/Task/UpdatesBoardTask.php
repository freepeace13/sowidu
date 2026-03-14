<?php

namespace Modules\Todos\Actions\Board\Task;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Todos\Models\Task;

class UpdatesBoardTask
{
    public function update($user, Task $task, array $params)
    {
        Gate::forUser($user)->authorize('update', $task);

        $validated = $this->validate($task, $params);

        $task->fill($validated)->save();

        return $task;
    }

    protected function validate($task, $params)
    {
        $groupNames = $task->board->settings()->groups()->all()->pluck('name');

        return Validator::make($params, [
            'title' => [
                Rule::requiredIf(function () use ($params) {
                    return blank($params['description'] ?? null) && blank($params['group'] ?? null);
                }),
                'string',
            ],
            'description' => [
                'nullable',
                Rule::requiredIf(function () use ($params) {
                    return blank($params['title'] ?? null) && blank($params['group'] ?? null);
                }),
                'string',
            ],
            'group' => [
                Rule::requiredIf(function () use ($params) {
                    return blank($params['description'] ?? null) && blank($params['title'] ?? null);
                }),
                'in:' . $groupNames->join(','),
            ],
        ])->validateWithBag('updateBoardTask');
    }
}
