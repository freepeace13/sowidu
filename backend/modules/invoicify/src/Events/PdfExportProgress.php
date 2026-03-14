<?php

namespace Modules\Invoicify\Events;

use App\Models\Company;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PdfExportProgress implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public $requester,
        public array $batch,
        public ?int $teamId = null,
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        if ($this->teamId) {
            $company = Company::find($this->teamId);
            $membership = $this->requester->teamMembership($company);

            if ($membership) {
                return [
                    new PrivateChannel($membership->broadcastChannel()),
                ];
            }
        }

        return [
            new PrivateChannel($this->requester->broadcastChannel()),
        ];
    }

    public function broadcastWith()
    {
        return [
            'batch' => $this->batch,
        ];
    }
}
