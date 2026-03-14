<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Modules\WorkLogs\Models\WorkLog;

interface WorkLogServiceInterface
{
    /**
     * @param  mixed  $user  The authenticated user
     * @param  mixed  $company  The company/team context
     */
    public function make(mixed $user, mixed $company);

    public function setQuery(Builder $query): self;

    public function newQuery(): Builder;

    /**
     * @param  mixed  $company  The company/team to filter by
     */
    public function onCompanyOnly(mixed $company): self;

    public function currentlyWorking(): self;

    /**
     * @param  mixed  $order  The order to filter by
     */
    public function onOrder(mixed $order): self;

    /**
     * @param  mixed  $causer  The employee/user to filter by
     */
    public function byEmployee(mixed $causer): self;

    public function filters(array $filters = []): self;

    /**
     * @return mixed The created activity log report
     */
    public function addReport(WorkLog $workLog, array $inputs): mixed;

    public function exists(): bool;
}
