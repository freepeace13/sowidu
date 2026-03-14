<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Http\Controllers;

use Illuminate\Http\Request;
use Modules\WorkLogs\Actions\CreateManualWorkLog;
use Modules\WorkLogs\Actions\DeleteManualWorkLog;
use Modules\WorkLogs\Actions\UpdateManualWorkLog;
use Modules\WorkLogs\Actions\UpdateWorkLogPaymentForm;
use Modules\WorkLogs\Contracts\External\ImpersonatorContract;
use Modules\WorkLogs\Models\WorkLog;

class ManualEntryWorkLogController extends Controller
{
    public function __construct(
        protected ImpersonatorContract $impersonator,
    ) {}

    public function store(Request $request)
    {
        CreateManualWorkLog::run($request->user(), $this->impersonator->getCompany(), $request->all());

        flash_success(trans('work_log.notifications.manual-entry.created'));

        return back();
    }

    public function update(Request $request, WorkLog $workLog)
    {
        UpdateManualWorkLog::run($request->user(), $this->impersonator->getCompany(), $workLog, $request->all());

        flash_success(trans('work_log.notifications.manual-entry.updated'));

        return back();
    }

    public function destroy(Request $request, WorkLog $workLog)
    {
        DeleteManualWorkLog::run($request->user(), $workLog);

        flash_success(trans('work_log.notifications.manual-entry.deleted'));

        return back();
    }

    public function updatePaymentForm(Request $request, WorkLog $workLog)
    {
        UpdateWorkLogPaymentForm::run($request->user(), $this->impersonator->getCompany(), $workLog, $request->all());

        flash_success(trans('work_log.notifications.manual-entry.payment-form'));
    }
}
