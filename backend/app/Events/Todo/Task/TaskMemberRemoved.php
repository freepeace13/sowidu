<?php

namespace App\Events\Todo\Task;

use App\Models\Employee;
use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
