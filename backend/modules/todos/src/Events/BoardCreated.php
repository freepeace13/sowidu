<?php

namespace Modules\Todos\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Todos\Models\Board;

class BoardCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Board $board,
    ) {
        if ($board->forTeam()) {
            CompanyBoardCreated::dispatch($board);
        }
    }
}
