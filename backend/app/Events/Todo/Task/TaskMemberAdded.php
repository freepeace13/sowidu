<?php

namespace App\Events\Todo\Task;

use App\Models\Employee;
use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskMemberAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User|Employee $causer;

    public function __construct(
        public Task $task,
        public User|Employee $user,
        public bool $causerHasJoined = false,
    ) {
        $this->causer = auth_user();
        $this->causerHasJoined = $user->is($this->causer);
    }
}
