<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\CreateWorkLogReport;
use App\Actions\Order\DeleteWorkLogReport;
use App\Actions\Order\UpdateWorkLogReport;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\ActivityLogReport;
use App\Models\Order;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;
use Modules\WorkLogs\Models\WorkLog;

class WorkLogReportController extends InertiaController
{
    use InteractsWithImpersonator;

    public function store(Request $request, Order $order, WorkLog $workLog)
    {
        app(CreateWorkLogReport::class)
            ->handle(
                $request->user(),
                $this->getCurrentCompany(),
                $order,
                $workLog,
                $request->all(),
            );

        return back();
    }

    public function update(Request $request, Order $order, WorkLog $workLog, ActivityLogReport $report)
    {
        $report = (new UpdateWorkLogReport)
            ->update(
                $request->user(),
                $this->getCurrentCompany(),
                $workLog,
                $report,
                $request->all(),
            );

        flash_success(
            $report->share_to_client ? 'Report has been shared to client.' : 'Report has been unshared to client.',
        );

        return back();
    }

    public function destroy(Request $request, WorkLog $workLog)
    {
        // (new DeleteWorkLogReport)->delete($request->user(), $workLog);

        return back();
    }
}
