<?php

namespace App\Services;

use App\Models\ActivityLogReport;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogReportService
{
    protected $query;

    public function __construct(protected User $user, protected Company $company)
    {
        $this->query = $this->newQuery();
    }

    /**
     * @return ActivityLogReport|static|Builder
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

    /** @return Builder|static|ActivityLogReport */
    public static function make(User $user, Company $company): static
    {
        return new static($user, $company);
    }

    /** @return Builder|static|ActivityLogReport */
    public function newQuery(): Builder
    {
        return ActivityLogReport::query()
            ->where('company_id', $this->company->id);
    }

    public function onOrder(Order $order): self
    {
        $this->query->whereHas(
            'workLog',
            fn (Builder $query) => $query->where('order_id', $order->id),
        );

        return $this;
    }
}
