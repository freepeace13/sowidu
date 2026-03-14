<?php

namespace App\Console\Commands\WorkLog;

use App\Enums\WorkLogEvent;
use Illuminate\Console\Command;
use Modules\WorkLogs\Models\WorkLog;

class SaveWorkLogEventTypeValuesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:save-work-log-event-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will save event type column values on work_logs table.';

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
        $this->withProgressBar(WorkLog::all(), function (WorkLog $workLog) {
            if ($workLog->isCurrentlyWorking()) {
                return $workLog->update(['event' => WorkLogEvent::CURRENTLY_WORKING()]);
            }

            if (blank($workLog->order_id)) {
                // Guess event type as sick!
                $workLog->update(['event' => WorkLogEvent::SICK()]);
            }
        });

        $this->info('Done saving work log events.');

        return 0;
    }
}
