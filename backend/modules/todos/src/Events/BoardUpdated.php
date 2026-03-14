<?php

namespace Modules\Todos\Events;

use App\Support\Models\InteractsWithModelChanges;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Todos\Models\Board;

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
