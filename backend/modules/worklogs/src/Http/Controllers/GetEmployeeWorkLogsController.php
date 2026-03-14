<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Http\Controllers;

use Illuminate\Http\Request;
use Modules\WorkLogs\Contracts\External\ImpersonatorContract;
use Modules\WorkLogs\Contracts\WorkLogRepositoryInterface;
use Modules\WorkLogs\Transformers\WorkLogTransformer;

class GetEmployeeWorkLogsController extends Controller
{
    public function __construct(
        protected WorkLogRepositoryInterface $workLogRepository,
        protected ImpersonatorContract $impersonator,
    ) {}

    public function __invoke(Request $request)
    {
        abort_unless(
            $this->impersonator->isImpersonating(),
            403,
            'You are not allowed to access this resource.',
        );

        $user = $request->user();

        $worklogs = $this->workLogRepository->make($user, $request->company())
            ->with([
                'causer',
                'order.deliveryAddress',
                'reports',
                'author',
            ]);

        $collection = $worklogs
            ->filters($request->only([
                'events',
                'employees',
                'dates',
                'order',
                'address',
                'timezone',
                'invoiceStatus',
                'q',
            ]))
            ->latest('started_at');

        return response()->json(
            [

                'workLogs' => $collection
                    ->paginate($request->get('count', 14))
                    ->through(function ($log) use ($user) {
                        return (new WorkLogTransformer($log))
                            ->withCauser($log->causer)
                            ->withOrder($log->order)
                            ->withReports($log->reports)
                            ->withEventMeta()
                            ->withActions(
                                $user->can('update', $log),
                                $user->can('delete', $log),
                            )
                            ->withWorkingStatus()
                            ->withIsInvoiced($log->isInvoiced())
                            ->resolve();
                    }),
                'totalHours' => $this->getTotalHours($worklogs, $request),
            ],

        );
    }

    public function getTotalHours($worklogs, $request)
    {
        $seconds = $worklogs
            ->filters($request->only([
                'events',
                'employees',
                'dates',
                'order',
                'address',
                'timezone',
                'invoiceStatus',
                'q',
            ]))->sum('duration_in_seconds');

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return __('work_log.labels.total_hours', [
            'hours' => $hours,
            'minutes' => $minutes,
        ]);
    }
}
