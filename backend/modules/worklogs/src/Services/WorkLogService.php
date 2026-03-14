<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Modules\WorkLogs\Contracts\External\ActivityLogReportContract;
use Modules\WorkLogs\Contracts\WorkLogServiceInterface;
use Modules\WorkLogs\Models\WorkLog;

class WorkLogService implements WorkLogServiceInterface
{
    protected $query;

    protected mixed $user;

    protected mixed $company;

    public function __construct(
        protected ActivityLogReportContract $activityLogReport,
    ) {}

    /**
     * @return WorkLog|static|Builder
     */
    public function __call($method, $parameters)
    {
        $result = $this->query->{$method}(...$parameters);

        if ($result instanceof Builder) {
            return $this;
        }

        $this->setQuery($this->newQuery());

        return $result;
    }

    public function make(mixed $user, mixed $company)
    {
        $this->user = $user;
        $this->company = $company;
        $this->query = $this->newQuery();

        return $this;
    }

    /** @return Builder|static */
    public function setQuery(Builder|\Illuminate\Database\Query\Builder $query): self
    {
        $this->query = $query;

        return $this;
    }

    /** @return Builder|static */
    public function newQuery(): Builder
    {
        return WorkLog::query()
            ->where('company_id', $this->company->id)
            ->where('user_id', $this->user->id);
    }

    public function onCompanyOnly(mixed $company): self
    {
        $this->setQuery(
            WorkLog::query()->where('company_id', $company->getKey()),
        );

        return $this;
    }

    public function currentlyWorking(): self
    {
        $this->query->whereNull('ended_at');

        return $this;
    }

    public function onOrder(mixed $order): self
    {
        $this->query->where('order_id', $order->id);

        return $this;
    }

    public function byEmployee(mixed $causer): self
    {
        $this->query->where('user_id', $causer->getKey());

        return $this;
    }

    public function filters(array $filters = []): self
    {
        $this->query
            ->when(
                $events = $filters['events'] ?? null,
                function ($query) use ($events) {
                    return $query->where(
                        fn ($query) => $query
                            ->whereIn('event', $events),
                    );
                },
            )
            ->when(
                $employees = $filters['employees'] ?? [],
                fn ($query) => $query->whereIn(
                    'user_id',
                    $employees,
                ),
            )
            ->when(
                $dates = collect(json_decode($filters['dates'] ?? '')),
                function ($query) use ($dates, $filters) {
                    if (!$dates->hasAny(['start', 'end'])) {
                        return;
                    }

                    $timezone = $filters['timezone'] ?? config('app.timezone');

                    $startDate = convert_to_timezone($dates->get('start'), $timezone);
                    $endDate = convert_to_timezone($dates->get('end'), $timezone);

                    // Single Date
                    if (
                        Carbon::parse($startDate)->equalTo(Carbon::parse($endDate))
                    ) {
                        return $query->where(
                            fn ($query) => $query->whereDate(
                                'started_at',
                                $startDate,
                            )
                                ->orWhereDate('ended_at', $endDate),
                        );
                    }

                    return $query->where(
                        fn ($query) => $query->whereBetween(
                            'started_at',
                            [
                                $startDate,
                                $endDate,
                            ],
                        )
                            ->orWhereBetween('ended_at', [
                                $startDate,
                                $endDate,
                            ]),
                    );
                },
            )
            ->when(
                $order = $filters['order'] ?? null,
                fn ($query) => $query->whereRelation(
                    'order',
                    'order_number',
                    'LIKE',
                    "%{$order}%",
                ),
            )
            ->when(
                $address = $filters['address'] ?? null,
                fn ($query) => $query->whereHas(
                    'order',
                    fn ($query) => $query->whereHas(
                        'deliveryAddress',
                        fn ($query) => $query->searchFullAddress($address),
                    ),
                ),
            );

        return $this;
    }

    public function addReport(WorkLog $workLog, array $inputs): mixed
    {
        return $this->activityLogReport->create(
            $workLog,
            $this->user,
            $this->company,
            $inputs,
        );
    }

    public function exists(): bool
    {
        return $this->query->exists();
    }
}
