<?php

namespace App\Events\Todo;

use App\Models\Board;
use App\Support\Models\InteractsWithModelChanges;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardUpdated
{
    use Dispatchable, InteractsWithModelChanges, InteractsWithSockets, SerializesModels;

    public $changes;

    public function __construct(
        public Board $board,
    ) {
        $this->changes = $this->getModelColumnChanges($board);
    }
}
