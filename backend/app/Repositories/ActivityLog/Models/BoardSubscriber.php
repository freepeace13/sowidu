<?php

namespace App\Repositories\ActivityLog\Models;

use App\Models\Subscriber;
use App\Support\Models\InteractsWithModelChanges;

class BoardSubscriber
{
    use InteractsWithModelChanges;

    protected $activity;

    public function __construct(
        protected Subscriber $subscriber,
        protected string $transKey = 'todo.activity.board.subscriber',
    ) {
        $this->activity = activity("todo.board.{$subscriber->board->id}")
            ->withProperties([
                'user' => $subscriber->user->full_name,
            ])
            ->on($subscriber->board);
    }

    public function added()
    {
        $this->activity
            ->event('added')
            ->log($this->transKey);
    }

    public function removed()
    {
        $this->activity
            ->event('removed')
            ->log($this->transKey);
    }
}
