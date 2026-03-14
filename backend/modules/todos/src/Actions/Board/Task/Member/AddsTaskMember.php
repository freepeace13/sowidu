<?php

namespace Modules\Todos\Actions\Board\Task\Member;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Todos\Models\Subscriber;
use Modules\Todos\Models\Task;

class AddsTaskMember
{
    public function add($user, Task $task, array $params)
    {
        Gate::forUser($user)->authorize('addMember', $task);

        $validated = $this->validate($task, $params);
        $task->addMember(
            Subscriber::with('user')->findOrFail($validated['subscriber_id'])->user,
        );
    }

    protected function validate($task, $params)
    {
        return Validator::make($params, [
            'subscriber_id' => ['required', 'exists:todo_subscribers,id'],
        ])->after(
            $this->ensureSubscibedAndNotYetMember($task),
        )->validateWithBag('addTaskMember');
    }

    protected function ensureSubscibedAndNotYetMember($task)
    {
        return function ($validator) use ($task) {
            $subscriberId = $validator->getData()['subscriber_id'] ?? null;

            if (!$subscriberId) {
                return;
            }

            $subscriber = $task->board->subscribers()->find($subscriberId);

            $validator->errors()->addIf(
                !$subscriber,
                'subscriber_id',
                'The user is not member of the board.',
            );

            if ($subscriber) {
                $validator->errors()->addIf(
                    $task->hasMember($subscriber->user),
                    'subscriber_id',
                    'The user is already member of the task.',
                );
            }
        };
    }
}
