<?php

namespace Modules\Todos\Actions\Board\Manual;

use App\Models\Employee;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Todos\Actions\Board\Group\CreatesBoardGroup;
use Modules\Todos\Actions\Board\Task\DuplicateBoardTask;
use Modules\Todos\Actions\Board\Task\Member\AddsTaskMember;
use Modules\Todos\Models\Board;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TodoManualTask;

class CreateManualTask
{
    public function __construct(
        private CreatesBoardGroup $createsBoardGroup,
        private AddsTaskMember $addsTaskMember,
    ) {}

    public function create($user, Board $board, array $params)
    {
        $validated = $this->validate($board, $params);
        $this->ensureGroupExists($user, $board, $validated['group']);

        if (Arr::has($validated, 'id')) {
            return (new DuplicateBoardTask)->duplicate($user, $board, Task::findOrFail($validated['id']));
        }

        if (array_key_exists('task_id', $validated)) {
            Gate::forUser($user)->authorize('canCreateSubTask', Task::findOrFail($validated['task_id']));
        } else {
            Gate::forUser($user)->authorize('createTask', $board);
        }

        $reporter = $board->getSubscription($user);

        $newTask = tap(new Task($validated), function ($task) use ($reporter) {
            $task->reporter()->associate($reporter);
        });

        $board->tasks()->save($newTask);

        if (!empty($validated['subscriber'])) {
            $this->addSubscribersToTask($user, $newTask, $validated['subscriber']);
        }

        $manualData = Arr::only($validated, ['start_date', 'finish_date', 'employee']);
        $this->createManualTask($newTask, $manualData);

        return $newTask;
    }

    protected function addSubscribersToTask($user, Task $task, array $userIds)
    {
        $subscriberMap = $task->board->subscribers()
            ->whereIn('user_id', $userIds)
            ->pluck('id', 'user_id');

        foreach ($userIds as $userId) {
            $subscriberId = $subscriberMap[$userId] ?? null;

            if (!$subscriberId) {
                continue;
            }

            $this->addsTaskMember->add($user, $task, [
                'subscriber_id' => $subscriberId,
            ]);
        }
    }

    protected function ensureGroupExists($user, Board $board, string $groupName)
    {
        if (!$board->settings()->groups()->has($groupName)) {
            $this->createsBoardGroup->create($user, $board, [
                'name' => $groupName,
                'color' => null,
            ]);
        }
    }

    protected function createManualTask(Task $task, array $manualTaskData)
    {
        $manualTask = new TodoManualTask([
            'start_date' => $manualTaskData['start_date'],
            'finish_date' => $manualTaskData['finish_date'],
        ]);

        $task->manualTasks()->save($manualTask);

        if (!empty($manualTaskData['employee']) && is_array($manualTaskData['employee'])) {
            $employeeIds = Employee::whereIn('user_id', $manualTaskData['employee'])
                ->pluck('id')
                ->toArray();

            if (!empty($employeeIds)) {
                $manualTask->employees()->attach($employeeIds);
            }
        }

        return $manualTask;
    }

    protected function validate(Board $board, array $params)
    {
        if (!array_key_exists('group', $params)) {
            $params['group'] = config('todo.board.settings.groups.fallback');
        }

        $validator = Validator::make($params, [
            'title' => 'required|string|max:255',
            'task_id' => [
                'nullable',
                'exists:todo_tasks,id',
                function ($attribute, $value, $fail) use ($board) {
                    if (!$value) {
                        return;
                    }

                    $parentTask = Task::with('board')->findOrFail($value);

                    if ($parentTask->board->isNot($board)) {
                        $fail('The parent task must belong to the same board. Please refresh and try again.');
                    }

                    if ($parentTask->isSubTask()) {
                        $fail('A subtask cannot have another subtask.');
                    }
                },
            ],
            'description' => 'nullable|string',
            'group' => 'required|string',
            'id' => 'nullable|exists:todo_tasks,id',
            'start_date' => 'required|date',
            'finish_date' => 'required|date|after_or_equal:start_date',
            'subscriber' => 'required|array',
            'subscriber.*' => 'required|exists:users,id',
        ]);

        $validator->after(function ($validator) use ($board, $params) {
            $userIds = $params['subscriber'] ?? [];

            if (!is_array($userIds)) {
                $validator->errors()->add('subscriber', 'Subscriber must be a list of user IDs.');

                return;
            }

            $validUserIds = $board->subscribers()->pluck('user_id')->toArray();
            $invalidUserIds = array_diff($userIds, $validUserIds);

            if (!empty($invalidUserIds)) {
                $validator->errors()->add('subscriber', 'One or more users are not members of this board.');
            }
        });

        return $validator->validateWithBag('createManualTask');
    }
}
