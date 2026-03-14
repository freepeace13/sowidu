<?php

namespace App\Events;

use App\Models\Company as Team;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrganizationProfileUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $team;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Team $team)
    {
        $this->team = $team;
    }
}
