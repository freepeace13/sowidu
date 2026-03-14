<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Modules\WorkLogs\Contracts\WorkLogRepositoryInterface;
use Modules\WorkLogs\Models\WorkLog;

class WorkLogRepository implements WorkLogRepositoryInterface
{
    protected $query;

    protected mixed $user;

    protected mixed $company;

    public function __construct(mixed $user = null, mixed $company = null)
    {
        $this->user = $user;
        $this->company = $company;

        if ($company) {
            $this->query = $this->newQuery();
        }
    }

    /**
     * @return WorkLog|static|Builder
     */
    public function __call(
        $method,
        $parameters,
    ) {
        $result = $this->query->{$method}(...$parameters);

        if ($result instanceof Builder) {
            return $this;
        }

        $this->setQuery($this->newQuery());

        return $result;
    }

    /** @return Builder|static|WorkLog|self */
    public static function make(
        mixed $user,
        mixed $company,
    ): static {
        return new static(
            $user,
            $company,
        );
    }

    /** @return Builder|static|WorkLog */
    public function setQuery(Builder|\Illuminate\Database\Query\Builder $query): self
    {
        $this->query = $query;

        return $this;
    }

    /** @return Builder|static|WorkLog */
    public function newQuery(): Builder
    {
        return WorkLog::query()
            ->where(
                'company_id',
                $this->company->id,
            );
    }

    /** @return Builder|static|WorkLog */
    public function includeOthers(bool $includeOthers)
    {
        if (!$includeOthers) {
            $this->query = WorkLog::query()
                ->where([
                    'company_id' => $this->company->id,
                    'user_id' => $this->user->id,
                ]);
        }

        return $this;
    }

    /** @return Builder|static|WorkLog */
    public function filters(array $filters = [])
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
                function ($query) use ($dates) {
                    $from = $dates->get('from');
                    $to = $dates->get('to');

                    if (!$dates->hasAny(['from', 'to']) || (!$from && !$to)) {
                        return;
                    }

                    $fromDate = null;
                    $toDate = null;
                    $timezone = $filters['timezone'] ?? config('app.default.timezone');

                    if ($from) {
                        $fromDate = Carbon::parse($from, $timezone)->setTimezone('UTC')->startOfDay();
                        // $fromDate = convert_to_timezone($fromDate, $timezone);
                    }

                    if ($to) {
                        $toDate = Carbon::parse($to, $timezone)->setTimezone('UTC')->endOfDay();
                        // $toDate = convert_to_timezone($toDate, $timezone);
                    }

                    // Empty To Date
                    if ($fromDate && !$toDate) {
                        return $query->where(
                            fn ($query) => $query->whereDate(
                                'started_at',
                                '>=',
                                $fromDate,
                            ),
                        );
                    }

                    // Empty From Date
                    if (!$fromDate && $toDate) {
                        return $query->whereDate(
                            'ended_at',
                            '<=',
                            $toDate,
                        );
                    }

                    return $query->where(
                        fn ($query) => $query->whereBetween('started_at', [$fromDate, $toDate])
                            ->orWhereBetween('ended_at', [$fromDate, $toDate]),
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
            )
            ->when(
                $q = $filters['q'] ?? null,
                fn (Builder $query) => $query->whereRelation(
                    'order',
                    'order_number',
                    'LIKE',
                    "%{$q}%",
                )
                    ->orWhereHas(
                        'order.deliveryAddress',
                        fn (Builder $query) => $query->searchFullAddress($q),
                    )
                    ->orWhereHas(
                        'user',
                        fn (Builder $query) => $query->search($q),
                    ),

            )
            ->when(
                $invoiceStatus = $filters['invoiceStatus'] ?? null,
                fn (Builder $query) => match ($invoiceStatus) {
                    'open' => $query->doesntHave('invoiceItem'),
                    'paid' => $query->where('is_paid', true),
                    'unpaid' => $query->where('is_paid', false)
                        ->has('invoiceItem'),
                },
            );

        return $this;
    }
}
