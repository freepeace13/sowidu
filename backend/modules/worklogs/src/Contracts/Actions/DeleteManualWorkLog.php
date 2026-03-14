<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts\Actions;

use Modules\WorkLogs\Models\WorkLog;

interface DeleteManualWorkLog
{
    /**
     * Delete a manual work log entry.
     *
     * @param  mixed  $user  The authenticated user
     * @param  WorkLog  $workLog  The work log to delete
     */
    public function handle(mixed $user, WorkLog $workLog);
}
