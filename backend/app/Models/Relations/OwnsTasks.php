<?php

namespace App\Models\Relations;

use App\Actions\CreateTask;
use App\Models\Task;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;

trait OwnsTasks
{
    /**
     * @return \App\Models\Task
     */
    public function createTask(Request $request)
    {
        return (new CreateTask($this))($request);
    }

    /**
     * Get the organization of the task.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'organization');
    }
}
