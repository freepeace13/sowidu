<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Contracts\External;

/**
 * Contract for company operations.
 *
 * Provides company-related functionality.
 */
interface CompanyContract
{
    /**
     * Get the company's currency.
     */
    public function getCurrency(mixed $company): string;

    /**
     * Get company employees.
     */
    public function getEmployees(mixed $company): mixed;

    /**
     * Find a company by ID.
     */
    public function find(int $id): mixed;
}
