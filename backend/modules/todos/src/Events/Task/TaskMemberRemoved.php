<?php

namespace Modules\Todos\Events\Task;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Todos\Models\Task;

class TaskMemberRemoved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Task $task,
        public User|Employee $user,
        public bool $causerHasLeft = false,
    ) {
        $this->causerHasLeft = $user->is(auth_user());
    }
}
