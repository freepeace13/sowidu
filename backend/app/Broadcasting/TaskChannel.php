<?php

namespace App\Broadcasting;

use App\Models\Task;
use Illuminate\Contracts\Auth\Access\Authorizable;

class TaskChannel
{
    public function join(Authorizable $user, $taskId): bool
    {
        $instance = Task::with(['board'])->findOrFail($taskId);

        if (!$user) {
            return false;
        }

        return $instance->hasMember($user) || $instance->creator?->is($user) || $instance->board->hasUser($user);
    }
}
