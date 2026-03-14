<?php

namespace Modules\Todos\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TodoManualTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'todo_task_id',
        'start_date',
        'finish_date',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'todo_task_id');
    }
}
