<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\ChangeWorkLogOrder;
use App\Actions\Order\WorkLog\CreateManualWorkLogEntry;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\Order\OrderTimeLogService;
use App\Services\Order\OrderWorkLogService;
use App\Transformers\Order\OrderTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\WorkLogs\Models\WorkLog;
use Modules\WorkLogs\Transformers\WorkLogTransformer;

class OrderTimeLogController extends Controller
{
    public function index(Request $request, Order $order)
    {
        $user = $request->user();
        $timeLogs = collect();

        if (inertia_wants_data(['timeLogs', 'totalTime'])) {
            $timeLogs = OrderWorkLogService::make(
                $order,
                $user,
                $request->company(),
            )
                ->canViewOthersWorkLog($user->can('viewOthersWorkLogs', $order))
                ->with(['causer', 'company', 'order', 'reports'])
                ->get();
        }

        return Inertia::render('Order/TimeLogs/OrderTimeLogs', [
            'order' => OrderTransformer::make($order)->resolve(),

            'timeLogs' => fn () => $timeLogs
                ->map(
                    fn ($log) => (new WorkLogTransformer($log))
                        ->withCanStopTime($request->user())
                        ->withCauser($log->causer)
                        ->withEmployeeRole()
                        ->withWorkingStatus()
                        ->withOrder($log->order)
                        ->withReports($log->reports)
                        ->withCanChangeOrder(
                            $user->can('canChangeWorkLogOrder', [$order, $log]),
                        )
                        ->withIsInvoiced($log->isInvoiced())
                        ->resolve(),
                ),

            'permissions' => fn () => [
                'can_share_time_logs' => OrderRepository::make(
                    $request->user(),
                    $request->company(),
                )->isContractorOn($order),
                'can_add_manual_time_log' => $user->can('addManualTimeLog', $order),
                'can_add_manual_time_log_to_others' => $user->can(
                    'createForOtherEmployees',
                    WorkLog::class,
                ),
            ],

            'totalTime' => fn () => OrderTimeLogService::make($order)
                ->getOrderTotalTimeRendered($timeLogs) ?? '',

            'employees' => Inertia::lazy(function () use ($request) {
                if (!$company = $request->company()) {
                    return [];
                }

                return $company
                    ->employees()
                    ->with('user')
                    ->get()
                    ->map(fn (Employee $employee) => (new UserTransformer($employee->user))
                        ->withAliasName($request->user())
                        ->resolve());
            }),
        ]);
    }

    public function update(Request $request, Order $order, WorkLog $workLog)
    {
        ChangeWorkLogOrder::run(
            $request->user(),
            $request->company(),
            $order,
            $workLog,
            $request->all(),
        );

        flash_success(trans('order.work_log.changed_order'));

        return back();
    }

    public function store(Request $request, Order $order)
    {
        CreateManualWorkLogEntry::run(
            $request->user(),
            $request->company(),
            $order,
            $request->all(),
        );

        flash_success(trans('order.work_log.manual-entry-created'));

        return back();
    }

    public function destroy($id)
    {
        //
    }
}
