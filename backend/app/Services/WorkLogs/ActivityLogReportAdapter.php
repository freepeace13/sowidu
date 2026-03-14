<?php

declare(strict_types=1);

namespace App\Services\WorkLogs;

use App\Models\ActivityLogReport;
use Modules\WorkLogs\Contracts\External\ActivityLogReportContract;
use Modules\WorkLogs\Models\WorkLog;

class ActivityLogReportAdapter implements ActivityLogReportContract
{
    public function create(WorkLog $workLog, mixed $user, mixed $company, array $inputs): mixed
    {
        $report = (new ActivityLogReport)->fill($inputs);

        $report->workLog()
            ->associate($workLog);

        $report->user()
            ->associate($user);

        $report->company()
            ->associate($company);

        $report->save();

        return $report;
    }
}
