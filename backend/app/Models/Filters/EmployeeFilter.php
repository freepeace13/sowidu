<?php

namespace App\Models\Filters;

use Account;
use EloquentFilter\ModelFilter;

class EmployeeFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    /**
     * Setup something before applying filters.
     *
     * @return void
     */
    public function setup()
    {
        $scope = $this->input('scope', null);

        if (is_null($scope)) {
            $this->push('scope', 'hired');
        }

        $this->query->confirmed();
    }

    /**
     * The scope result query filter.
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scope(string $value)
    {
        switch ($value) {
            case 'all':
                return $this->query;
            case 'others':
                return $this->applyOthersScopeQuery();
            case 'hired':
                return $this->applyHiredScopeQuery();
        }
    }

    /**
     * Apply 'others' scope query only includes others employee.
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected function applyOthersScopeQuery()
    {
        if ($company = Account::secondary()) {
            return $this->where('company_id', '!=', $company->id);
        }
    }

    /**
     * Apply 'onboards' scope query only employees of current account.
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected function applyHiredScopeQuery()
    {
        if ($company = Account::secondary()) {
            return $this->where('company_id', $company->id);
        }
    }
}
