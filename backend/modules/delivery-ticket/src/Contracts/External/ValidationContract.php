<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Contracts\External;

/**
 * Contract for external validation rules.
 *
 * Provides validation rules that depend on main app.
 */
interface ValidationContract
{
    /**
     * Get the OwnedByCompany validation rule.
     *
     * @param  string  $modelClass  The model class to check ownership against
     * @param  string  $columnName  The column name used to check company ownership
     * @return mixed The validation rule instance
     */
    public function ownedByCompanyRule(string $modelClass, string $columnName = 'company_id'): mixed;
}
