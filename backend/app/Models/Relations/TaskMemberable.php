<?php

namespace App\Models\Relations;

use App\Models\Task;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait TaskMemberable
{
    /**
     * Get the assigned tasks of the member.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tasks(): MorphToMany
    {
        return $this->morphToMany(
            Task::class,
            'model',
            'memberables',
        );
    }
}
