<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts\Actions;

use Modules\WorkLogs\Models\WorkLog;

interface CreateManualWorkLog
{
    /**
     * Create a manual work log entry.
     *
     * @param  mixed  $user  The authenticated user creating the entry
     * @param  mixed  $company  The company/team context
     * @param  array  $inputs  The work log data
     */
    public function handle(mixed $user, mixed $company, array $inputs): WorkLog;
}
