<?php

namespace App\Models\Relations;

use App\Models\Activity;
use App\Models\ActivityLogReport;
use Modules\WorkLogs\Models\WorkLog;

trait HasLogs
{
    public function logs()
    {
        return Activity::inLog("order.{$this->id}");
    }

    public function reports()
    {
        return $this->hasManyThrough(
            ActivityLogReport::class,
            WorkLog::class,
            'order_id',
            'work_log_id',
        );
    }

    public function sharedToClientReports()
    {
        return $this->reports()->sharedToClient();
    }

    public function workLogs()
    {
        return $this->hasMany(WorkLog::class);
    }
}
