<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Enums\WorkLogEvent;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Modules\WorkLogs\Contracts\WorkLogServiceInterface;
use Modules\WorkLogs\Models\WorkLog;

class OrderTimeLogService
{
    public function __construct(
        protected Order $order,
        protected ?WorkLogServiceInterface $workLogService = null,
    ) {
        $this->workLogService = $workLogService ?? app(WorkLogServiceInterface::class);
    }

    public static function make(Order $order): static
    {
        return new static($order);
    }

    public function start(User $user, Company $company): WorkLog
    {
        $workLog = (new WorkLog)->fill([
            'started_at' => now(),
            'event' => WorkLogEvent::CURRENTLY_WORKING(),
        ]);
        $workLog->order()
            ->associate($this->order);
        $workLog->causer()
            ->associate($user);
        $workLog->company()
            ->associate($company);

        return tap($workLog)->save();
    }

    public function stop(WorkLog $workLog, User $user, Company $company): WorkLog
    {
        $endedAt = now();
        $duration = $endedAt->diffInSeconds(Carbon::parse($workLog->started_at));

        return tap($workLog)->update([
            'ended_at' => $endedAt,
            'duration_in_seconds' => $duration,
            'event' => WorkLogEvent::FINISHED_WORKING(),
        ]);
    }

    public function manualEntry(User $user, Company $company, Order $order, array $inputs): WorkLog
    {
        $diffInSeconds = Carbon::parse($inputs['ended_at'])
            ->diffInSeconds(Carbon::parse($inputs['started_at']));

        $workLog = WorkLog::make(Arr::only($inputs, ['started_at', 'ended_at']));

        $workLog->duration_in_seconds = $diffInSeconds;
        $workLog->event = WorkLogEvent::FINISHED_WORKING();

        if (filled($employee = data_get($inputs, 'employee'))) {
            $workLog->user()
                ->associate($employee);
        } else {
            $workLog->user()
                ->associate($user);
        }

        $workLog->company()
            ->associate($company);
        $workLog->author()
            ->associate($user);
        $workLog->order()
            ->associate($order);

        return tap($workLog)->save();
    }

    public function timeLogs(): Builder
    {
        return WorkLog::query()->where('order_id', $this->order->id);
    }

    protected function durationForHuman($duration)
    {
        if ($duration === 0) {
            return null;
        }

        CarbonInterval::enableFloatSetters();

        $totalHours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);

        $formattedInterval = CarbonInterval::hours($totalHours)->minutes($minutes);

        return $formattedInterval->forHumans([
            'join' => ' and ',
            'parts' => 2,
            'minimumUnit' => 'minute',
        ]);
    }

    public function getOrderTotalTimeRendered($timeLogs = []): ?string
    {
        if (empty($timeLogs)) {
            $timeLogs = $this->timeLogs();
        }

        return $this->durationForHuman(
            $timeLogs->sum('duration_in_seconds'),
        );
    }

    public function employeeCanStartWorking(User $user, Company $company): bool
    {
        $orderStatus = $this->order->status;

        return $orderStatus === OrderStatus::COMMISSIONED ||
               $orderStatus === OrderStatus::STARTED ||
               $orderStatus === OrderStatus::ONGOING;
    }

    public function useCanStartTimeTrack(User $user, Company $company)
    {
        return $this->employeeCanStartWorking($user, $company)
            && !$this->currentEmployeeIsStillWorkingOnOrder($user, $company);
    }

    public function currentEmployeeIsStillWorkingOnOrder(User $user, Company $company)
    {
        return $this->workLogService->make($user, $company)
            ->currentlyWorking()
            ->onOrder($this->order)
            ->byEmployee($user)
            ->exists();
    }

    public function userCanStopTimeTrackOnOrder(User $user, Company $company, Order $order)
    {
        return $this->workLogService->make($user, $company)
            ->currentlyWorking()
            ->onOrder($order)
            ->byEmployee($user)
            ->exists();
    }

    public function userIsCurrentlyWorkingOnOrder(User $user, Company $company, Order $order)
    {
        return $this->userCanStopTimeTrackOnOrder($user, $company, $order);
    }

    public function userCanSubmitForReview(User $user, Company $company, Order $order): bool
    {
        return !$this->employeesStillWorking($user, $company, $order);
    }

    public function employeesStillWorking(User $user, Company $company, Order $order)
    {
        return $this->workLogService->make($user, $company)
            ->onCompanyOnly($company)
            ->onOrder($order)
            ->currentlyWorking()
            ->exists();
    }
}
