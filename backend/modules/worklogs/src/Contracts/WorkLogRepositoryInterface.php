<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface WorkLogRepositoryInterface
{
    /**
     * @param  mixed  $user  The authenticated user
     * @param  mixed  $company  The company/team context
     */
    public static function make(mixed $user, mixed $company): self;

    public function setQuery(Builder $query): self;

    public function newQuery(): Builder;

    public function includeOthers(bool $includeOthers);

    public function filters(array $filters = []);

    public function __call($method, $parameters);
}
