<?php

namespace App\Models;

use App\Support\Todo\TodoHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTimeLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'todo_task_time_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'board_id',
        'task_id',
        'author_id',
        'date',
        'duration',
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'duration' => 'datetime:H:i',
    ];

    public function getDurationAttribute($duration)
    {
        return $duration;
    }

    /** Get the duration text equivalent */
    public function getDurationTextAttribute(): string
    {
        return TodoHelper::durationForHumans($this?->duration ?? $this->total_duration);
    }

    public function scopeFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($sortBy = $filters['sortBy'] ?? null, function (Builder $q) use ($filters, $sortBy) {
                $descending = $filters['descending'] ?? false;
                $q->orderBy($sortBy, $descending ? 'desc' : 'asc');
            })
            ->when($filters['groupByUser'] ?? null, fn ($q) => $q->selectRaw('author_id, SEC_TO_TIME( SUM(TIME_TO_SEC(duration) ) ) as total_duration')->groupBy('author_id'));
    }

    public function isOwner($user)
    {
        return $this->loadMissing('author.user')->author->user->is($user);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function author()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
