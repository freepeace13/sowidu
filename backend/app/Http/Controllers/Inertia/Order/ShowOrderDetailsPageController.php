<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Enums\Permissions;
use App\Http\Controllers\Inertia\InertiaController;
use App\Http\Controllers\Traits\WithOrderService;
use App\Models\Order;
use App\Services\ActivityLogReportService;
use App\Services\Order\OrderTimeLogService;
use App\Services\Order\OrderWorkLogService;
use App\Traits\InteractsWithImpersonator;
use App\Transformers\ActivityLogReportTransformer;
use App\Transformers\ActivityTransformer;
use App\Transformers\Order\OrderTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\WorkLogs\Transformers\WorkLogTransformer;

class ShowOrderDetailsPageController extends InertiaController
{
    use InteractsWithImpersonator, WithOrderService;

    public function __invoke(Request $request, Order $order)
    {
        $service = $this->createOrderService();
        $timeLogsService = new OrderTimeLogService($order);

        $user = $request->user();
        $currentCompany = $this->getCurrentCompany();

        abort_unless(
            $service->isOrderedByCurrentUser($order) || $service->isCurrentlyOwned($order),
            404,
            'Order not found!',
        );

        $viewerIsClient = $order->client->is($currentCompany ?? $user);

        return Inertia::render('Order/Show', [
            'order' => (new OrderTransformer($order))
                ->withClientFullDetails($order->client)
                ->withDeliveryAddress()
                ->withContractorDetails()
                ->withRequiresResponse($user, $currentCompany)
                ->withTotalTimeRendered($timeLogsService->getOrderTotalTimeRendered())
                ->resolve(),

            'orderOn' => $service->isOrderedByCurrentUser($order) ? 'outgoing' : 'incoming',

            'timeLogs' => fn () => OrderWorkLogService::make($order, $user, $currentCompany)
                ->with(['causer', 'company', 'order', 'reports'])
                ->get()
                ->map(
                    fn ($log) => (new WorkLogTransformer($log))
                        ->withCanStopTime($user)
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

            'timelines' => fn () => $service
                ->orderTimelines($order)
                ->with(['causer'])
                ->latest()
                ->get()
                ->map(
                    fn ($timeline) => (new ActivityTransformer($timeline))->resolve(),
                ),

            'timeTrack' => !$currentCompany ? [
                'auth_can_start' => false,
                'auth_can_stop' => false,
                'auth_is_currently_working' => false,
                'auth_can_submit_for_review' => false,
            ] : [
                'auth_can_start' => $timeLogsService->useCanStartTimeTrack($user, $currentCompany),
                'auth_can_stop' => $timeLogsService->userCanStopTimeTrackOnOrder(
                    $user,
                    $currentCompany,
                    $order,
                ),
                'auth_is_currently_working' => $timeLogsService->userIsCurrentlyWorkingOnOrder(
                    $user,
                    $currentCompany,
                    $order,
                ),
                'auth_can_submit_for_review' => $timeLogsService->userCanSubmitForReview(
                    $user,
                    $currentCompany,
                    $order,
                ),
            ],

            'reports' => fn () => $viewerIsClient ?
                $order->sharedToClientReports()
                    ->get()
                    ->map(
                        fn ($report) => (new ActivityLogReportTransformer($report))
                            ->withUser($report->user)
                            ->resolve(),
                    )
                    ->toArray()
                : ActivityLogReportService::make($user, $currentCompany)
                    ->onOrder($order)
                    ->with(['user'])
                    ->when($viewerIsClient, fn ($query) => $query->sharedToClient())
                    ->get()
                    ->map(
                        fn ($report) => (new ActivityLogReportTransformer($report))
                            ->withUser($report->user)
                            ->resolve(),
                    ),

            'viewer' => fn () => [
                'is_client' => $viewerIsClient,
                'can_view_reports' => $viewerIsClient ? $order->sharedToClientReports()
                    ->exists()
                    : true,
                'can_update_client' => !$viewerIsClient ? $this->getCurrentEmployee()
                    ->can(Permissions::CAN_CREATE_ORDER)
                    : false,
                'can_update_order' => $service->canUpdateOrder($order),
            ],
        ]);
    }
}
