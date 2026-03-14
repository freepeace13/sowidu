<?php

namespace App\Models\Filters;

use App\Models\Company;
use App\Models\User;
use App\Support\Facades\Impersonate;
use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class OrderFilter extends ModelFilter
{
    protected bool $isImpersonating;

    public function __construct()
    {
        parent::__construct(...func_get_args());

        $this->isImpersonating = Impersonate::isImpersonating();
    }

    /**
     * Filter using the initial names
     */
    public function initial($value)
    {
        return $this->when(
            filled($value),
            function (Builder $query) use ($value) {
                $query->when(
                    $this->isImpersonating,
                    function (Builder $query) use ($value) {
                        // Current user is impersonating - query using client
                        $query->whereHasMorph(
                            'client',
                            [User::class],
                            fn ($q) => $q->where('first_name', 'LIKE', "$value%"),
                        )
                            ->orWhereHasMorph(
                                'contractor',
                                [Company::class],
                                fn ($q) => $q->where('name', 'LIKE', "$value%"),
                            );
                    },
                    function (Builder $query) use ($value) {
                        // Not impersonating - query using client
                        $query->whereHasMorph(
                            'contractor',
                            [Company::class],
                            fn ($query) => $query->where('name', 'LIKE', "$value%"),
                        );
                    },
                );
            },
        );
    }

    public function q($search)
    {
        return $this->where(
            fn ($query) => $query
                ->search($search)
                ->orWhereHas(
                    'deliveryAddress',
                    function (Builder $query) use ($search) {
                        $query->searchFullAddress($search);
                    },
                ),
        );
    }

    public function status($value)
    {
        return $this->when(
            filled($value),
            fn (Builder $query) => $query->where('status', $value),
        );
    }

    public function dates($values)
    {
        $dates = collect($values);

        if (
            !$dates->hasAny([
                'start',
                'end',
            ])
        ) {
            return;
        }

        $startDate = $dates->get('start');
        $endDate = $dates->get('end');

        // Single Date
        if (
            Carbon::parse($startDate)->equalTo(Carbon::parse($endDate))
        ) {
            return $this->where(
                fn ($query) => $query->whereDate('planned_start_date', $startDate)
                    ->orWhereDate('planned_finish_date', $endDate),
            );
        }

        $this->where(
            fn ($query) => $query->whereBetween(
                'planned_start_date',
                [
                    $dates->get('start'),
                    $dates->get('end'),
                ],
            )
                ->orWhereBetween('planned_finish_date', [
                    $dates->get('start'),
                    $dates->get('end'),
                ]),
        );
    }

    public function dateAdded($dates)
    {
        $dates = collect($dates);

        if (
            !$dates->hasAny([
                'from',
                'to',
            ])
        ) {
            return;
        }

        $from = $dates->get('from');
        $to = $dates->get('to');

        if (blank($from) && blank($to)) {
            return;
        }

        // Single Date
        if (
            Carbon::parse($from)->equalTo(Carbon::parse($to))
        ) {
            return $this->where(
                fn ($query) => $query->whereDate('created_at', $from),
            );
        }

        $this->where(
            fn ($query) => $query->whereBetween(
                'created_at',
                [
                    $from,
                    $to,
                ],
            ),
        );

    }

    public function started($dates)
    {
        $dates = collect($dates);

        if (
            !$dates->hasAny([
                'from',
                'to',
            ])
        ) {
            return;
        }

        $from = $dates->get('from');
        $to = $dates->get('to');

        if (blank($from) && blank($to)) {
            return;
        }

        // Single Date
        if (
            Carbon::parse($from)->equalTo(Carbon::parse($to))
        ) {
            return $this->where(
                fn ($query) => $query->whereDate('planned_start_date', $from),
            );
        }

        $this->where(
            fn ($query) => $query->whereBetween(
                'planned_start_date',
                [
                    $from,
                    $to,
                ],
            ),
        );

    }

    public function plannedFinished($dates)
    {
        $dates = collect($dates);

        if (
            !$dates->hasAny([
                'from',
                'to',
            ])
        ) {
            return;
        }

        $from = $dates->get('from');
        $to = $dates->get('to');

        if (blank($from) && blank($to)) {
            return;
        }

        // Single Date
        if (
            Carbon::parse($from)->equalTo(Carbon::parse($to))
        ) {
            return $this->where(
                fn ($query) => $query->whereDate('planned_finish_date', $from),
            );
        }

        $this->where(
            fn ($query) => $query->whereBetween(
                'planned_finish_date',
                [
                    $from,
                    $to,
                ],
            ),
        );

    }
}
