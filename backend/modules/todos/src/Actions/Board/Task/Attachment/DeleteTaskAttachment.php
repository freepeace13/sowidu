<?php

namespace Modules\Todos\Actions\Board\Task\Attachment;

use Illuminate\Support\Facades\Gate;
use Modules\Todos\Models\Task;
use Modules\Todos\Models\TaskAttachment;

class DeleteTaskAttachment
{
    public function delete($user, Task $task, TaskAttachment $taskAttachment)
    {
        Gate::forUser($user)->authorize('destroyAttachment', [$task, $taskAttachment]);

        $taskAttachment->delete();
    }
}
