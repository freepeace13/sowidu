<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts\Actions;

use Modules\WorkLogs\Models\WorkLog;

interface UpdateManualWorkLog
{
    /**
     * Update a manual work log entry.
     *
     * @param  mixed  $user  The authenticated user
     * @param  mixed  $company  The company/team context
     * @param  WorkLog  $workLog  The work log to update
     * @param  array  $inputs  The updated data
     */
    public function handle(mixed $user, mixed $company, WorkLog $workLog, array $inputs);
}
