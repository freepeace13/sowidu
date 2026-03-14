<?php

namespace App\Repositories\Catalog;

use App\Models\CatalogItem;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CatalogItemRepository
{
    protected $query;

    public function __construct(protected User $user, protected Company $company)
    {
        $this->query = $this->newQuery();
    }

    public static function make(User $user, Company $company)
    {
        return new static($user, $company);
    }

    public function setQuery(Builder $query)
    {
        $this->query = $query;

        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function newQuery()
    {
        return CatalogItem::query()
            ->where([
                'company_id' => $this->company->getKey(),
            ]);
    }

    /**
     * @return \App\Models\Order|static
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
}
