<?php

namespace App\Console\Commands\Utils;

use App\Enums\OrderEvent;
use App\Models\Activity;
use App\Models\ActivityLogReport;
use App\Models\Company;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Modules\WorkLogs\Models\WorkLog;

class ConvertActivityLogToWorkLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert-activity-to-work-log {--truncate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will convert activity_log time tracks to work_logs.';

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
        if ($this->option('truncate')) {
            WorkLog::truncate();
        }

        // Fetch all companies
        Company::all()
            ->each(function (Company $company) {
                // Get company orders
                Order::query()
                    ->whereTeamId($company->id)
                    ->whereHasMorph(
                        'contractor',
                        [Company::class],
                        fn ($q) => $q->where('id', $company->getKey()),
                    )
                    ->get()
                    ->each(function (Order $order) {
                        // Fetch order activity_log
                        Activity::query()
                            ->where(function (Builder $query) {
                                $query
                                    ->where('event', OrderEvent::START_WORKING())
                                    ->orWhere('event', OrderEvent::FINISH_WORKING());
                            })
                            ->where('subject_type', (new Order)->getMorphClass())
                            ->where('subject_id', $order->getKey())
                            ->oldest()
                            ->get()
                            ->each(function (Activity $activity) {
                                $properties = $activity->properties;

                                $orderId = $activity->subject_id;
                                $userId = $activity->causer_id;

                                $companyId = $properties->get('company_id', null);

                                if (!$companyId) {
                                    $this->error("Order work log properties has no company_id! Activity: {$activity->id}");

                                    return;
                                }

                                // $isCurrentlyWorking = $properties->get('currently_working', false);

                                if ($activity->event === OrderEvent::START_WORKING()) {
                                    $startedAt = $activity->created_at;

                                    // Save to WorkLog
                                    $workLog = new WorkLog;
                                    $workLog->order_id = $activity->subject_id;
                                    $workLog->user_id = $activity->causer_id;
                                    $workLog->company_id = $companyId;
                                    $workLog->started_at = $startedAt;
                                    $workLog->ended_at = null;
                                    $workLog->created_at = $startedAt;
                                    $workLog->save();

                                    $this->info('Creating work_log, event: started.');

                                    $this->saveLogReports($activity, $workLog);

                                    return;
                                }

                                if ($activity->event === OrderEvent::FINISH_WORKING()) {
                                    $endedAt = $activity->created_at;

                                    $duration = $properties->get('duration_in_seconds', null);

                                    // Find the WorkLog for this activity
                                    $startWorkLog = WorkLog::query()
                                        ->where([
                                            'user_id' => $userId,
                                            'company_id' => $companyId,
                                            'order_id' => $orderId,
                                        ])
                                        ->whereNull('ended_at')
                                        ->first();

                                    if (!$startWorkLog) {
                                        $this->error("Start work log not found! Order: {$orderId}, Company: {$companyId}, User: {$userId}, Activity: {$activity->id}");

                                        return;
                                    }

                                    $startWorkLog->fill([
                                        'ended_at' => $endedAt,
                                        'duration_in_seconds' => $duration,
                                    ]);

                                    $startWorkLog->updated_at = $endedAt;
                                    $startWorkLog->save();

                                    $this->saveLogReports($activity, $startWorkLog);

                                    $this->info('Work log has been created successfully.');
                                }
                            });
                    });
            });

        return 0;
    }

    protected function saveLogReports(Activity $activity, WorkLog $workLog)
    {
        ActivityLogReport::query()
            ->where('activity_log_id', $activity->id)
            ->update([
                'work_log_id' => $workLog->id,
            ]);
    }
}
