<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Actions;

use Modules\WorkLogs\Contracts\Actions\DeleteManualWorkLog as DeleteManualWorkLogContract;
use Modules\WorkLogs\Contracts\External\AuthorizationContract;
use Modules\WorkLogs\Models\WorkLog;
use Modules\WorkLogs\Traits\AsAction;

class DeleteManualWorkLog implements DeleteManualWorkLogContract
{
    use AsAction;

    public function __construct(
        protected AuthorizationContract $authorization,
    ) {}

    public function handle(mixed $user, WorkLog $workLog)
    {
        $this->authorization->authorize($user, 'delete', $workLog);

        $workLog->delete();
    }
}
