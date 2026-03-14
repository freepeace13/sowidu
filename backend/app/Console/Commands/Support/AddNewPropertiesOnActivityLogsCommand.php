<?php

namespace App\Console\Commands\Support;

use App\Enums\OrderEvent;
use App\Models\Order;
use App\Services\Order\OrderTimeLogService;
use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;

class AddNewPropertiesOnActivityLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:seed-activity-log-properties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will seed new properties data on activity logs.';

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
        Activity::query()
            ->where('subject_type', (new Order)->getMorphClass())
            ->get()
            ->each(function (Activity $activity) {
                // Get contractor and save to properties
                $order = $activity->subject()->with(['contractor'])->first();
                $contractor = $order->contractor;

                if ($activity->event === OrderEvent::START_WORKING()) {
                    $hasAlreadyFinished = Activity::query()
                        ->where('subject_type', (new Order)->getMorphClass())
                        ->where('subject_id', $activity->subject_id)
                        ->where('event', OrderEvent::FINISH_WORKING())
                        ->where('id', '>', $activity->id)
                        ->exists();

                    $activity->update([
                        'properties' => array_merge(
                            $activity->properties->toArray(),
                            [
                                'currently_working' => !$hasAlreadyFinished,
                            ],
                        ),
                    ]);
                }

                $activity->update([
                    'properties' => array_merge(
                        $activity->properties->toArray(),
                        [
                            'company_id' => $contractor->id,
                        ],
                    ),
                ]);

                OrderTimeLogService::make($order)->saveDuration($activity);

                $this->comment('Company ID is added on order: ' . $activity->subject_id);
            });

        $this->info('Done seeding.');

        return 0;
    }
}
