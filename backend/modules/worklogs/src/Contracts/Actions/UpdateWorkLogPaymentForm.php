<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts\Actions;

use Modules\WorkLogs\Models\WorkLog;

interface UpdateWorkLogPaymentForm
{
    /**
     * Update the payment form of a work log.
     *
     * @param  mixed  $user  The authenticated user
     * @param  mixed  $company  The company/team context
     * @param  WorkLog  $workLog  The work log to update
     * @param  array  $inputs  The updated payment data
     */
    public function handle(mixed $user, mixed $company, WorkLog $workLog, array $inputs);
}
