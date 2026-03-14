<?php

namespace App\Services\Order;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Modules\WorkLogs\Models\WorkLog;

class OrderWorkLogService
{
    protected Builder $query;

    public function __construct(
        protected Order $order,
        protected User $user,
        protected ?Company $company,
    ) {
        $this->query = $this->newQuery();
    }

    /** @return Builder|static|WorkLog|self */
    public static function make(Order $order, User $user, ?Company $company = null): static
    {
        return new static($order, $user, $company);
    }

    protected function newQuery()
    {
        return WorkLog::query()
            ->where([
                'order_id' => $this->order->getKey(),
            ])
            ->when(
                filled($this->company) && $this->userIsContractor(),
                fn (Builder $query) => $query->where('company_id', $this->company->getKey()),
                fn (Builder $query) => $query->where('is_shared', true),
            );
    }

    protected function userIsContractor()
    {
        return $this->order->contractor->is($this->company);
    }

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

    /** @return Builder|static */
    public function setQuery(Builder|\Illuminate\Database\Query\Builder $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return Builder|static|WorkLog
     */
    public function canViewOthersWorkLog(bool $canViewOthersWorkLog = false)
    {
        if (!$canViewOthersWorkLog) {
            return $this->query->where('user_id', $this->user->getKey());
        }

        return $this->query;
    }
}
