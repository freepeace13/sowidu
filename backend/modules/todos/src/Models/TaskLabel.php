<?php

namespace Modules\Todos\Models;

use Illuminate\Database\Eloquent\Model;

class TaskLabel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'todo_task_labels';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'task_id',
        'label_id',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the Label
     *
     * @param  string  $value
     * @return string
     */
    public function getLabelAttribute()
    {
        return $this->task->board->settings()
            ->labels()
            ->find($this->label_id);
    }
}
