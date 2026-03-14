<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts\External;

use Modules\WorkLogs\Models\WorkLog;

/**
 * Outgoing port for activity log report management.
 *
 * The main application provides the ActivityLogReport model adapter.
 */
interface ActivityLogReportContract
{
    /**
     * Create a new activity log report for a work log.
     *
     * @param  WorkLog  $workLog  The work log to attach the report to
     * @param  mixed  $user  The user creating the report
     * @param  mixed  $company  The company context
     * @param  array  $inputs  The report data
     * @return mixed The created report
     */
    public function create(WorkLog $workLog, mixed $user, mixed $company, array $inputs): mixed;
}
