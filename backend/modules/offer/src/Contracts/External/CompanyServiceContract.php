<?php

namespace Modules\Offer\Contracts\External;

use Illuminate\Database\Eloquent\Model;

/**
 * Outgoing port for company-related services needed by the Offer module.
 */
interface CompanyServiceContract
{
    /**
     * Find a company by ID.
     */
    public function find(int $id): ?Model;

    /**
     * Find a company by ID or fail.
     */
    public function findOrFail(int $id): Model;

    /**
     * Transform company with full details for offer (tax settings, invoice defaults, current address, type).
     */
    public function transformWithFullDetails(Model $company): array;

    /**
     * Transform company for recipient details (current address, owner details, type).
     */
    public function transformForRecipient(Model $company): array;

    /**
     * Get company's current place.
     */
    public function getCurrentPlace(Model $company): ?Model;
}
