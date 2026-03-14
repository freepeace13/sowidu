<?php

namespace Modules\Todos\Actions\Board\Task\Member;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\Todos\Models\Subscriber;
use Modules\Todos\Models\Task;

class RemovesTaskMember
{
    // NOTE: Dili pa final possible ma uncomment or mausab ang logic - okay pre
    public function remove(User|Employee $user, Task $task, Subscriber $subscriber)
    {
        Gate::forUser($user)->authorize('removeMember', $task);

        abort_unless($task->board->is($subscriber->board), 404, 'Subscriber not found.');

        $task->removeMember($subscriber);
    }
}
