<?php

namespace App\Repositories\ActivityLog\Models;

use App\Models\Board as BoardModel;
use App\Support\Models\InteractsWithModelChanges;
use Spatie\Activitylog\Models\Activity;

class Board
{
    use InteractsWithModelChanges;

    protected $activity;

    public function __construct(
        protected BoardModel $board,
        protected string $log = 'todo.activity.board',
    ) {
        $this->activity = activity("todo.board.{$board->id}")
            ->on($this->board);
    }

    public function created()
    {
        $this->activity
            ->event('created')
            ->log($this->log);
    }

    public function updated(array $changes = [])
    {
        collect($changes)
            ->map(
                fn ($val, $column) => $this->activity
                    ->event("updated.{$column}")
                    ->log($this->log),
            );
    }

    public function deleted()
    {
        Activity::inLog("todo.board.{$this->board->id}")->delete(); // Remove all logs on this board
    }
}
