<?php

namespace Modules\Invoicify\Events;

use App\Models\Company;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PdfExportCompleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public $requester,
        public string $fileUrl,
        public string $fileName,
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
                $channel = $membership->broadcastChannel();
                logger()->info('PdfExportCompleted: Broadcasting on membership channel', [
                    'team_id' => $this->teamId,
                    'channel' => $channel,
                    'membership_id' => $membership->id,
                ]);

                return new PrivateChannel($channel);
            }
        }

        $channel = $this->requester->broadcastChannel();
        logger()->info('PdfExportCompleted: Broadcasting on user channel', [
            'team_id' => $this->teamId,
            'channel' => $channel,
            'requester_id' => $this->requester->id,
        ]);

        return new PrivateChannel($channel);
    }

    public function broadcastWith()
    {
        $data = [
            'file_url' => $this->fileUrl,
            'file_name' => $this->fileName,
        ];
        logger()->info('PdfExportCompleted: Broadcasting with data', $data);

        return $data;
    }
}
