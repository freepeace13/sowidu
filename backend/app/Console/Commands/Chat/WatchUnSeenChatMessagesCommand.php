<?php

namespace App\Console\Commands\Chat;

use App\Notifications\Chat\UnReadMessageNotification;
use Illuminate\Console\Command;
use Musonza\Chat\Models\MessageNotification;
use Musonza\Chat\Models\Participation;

class WatchUnSeenChatMessagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:unseen-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will generate notification for the user whose not seen the new message.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        MessageNotification::query()
            ->where('is_seen', 0)
            ->get(['is_seen', 'id', 'participation_id'])
            ->each(function ($messageNotification) {
                $participation = Participation::with([
                    'messageable',
                ])->find($messageNotification->participation_id);

                if (!$participation) {
                    return;
                }

                $participation?->messageable?->notify(
                    new UnReadMessageNotification($messageNotification),
                );
            });

        return 0;
    }
}
