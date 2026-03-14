<?php

namespace Modules\Catalog\Contracts\External;

use App\Models\Company;

/**
 * Outgoing port for media management used by the Catalog module.
 *
 * This contract abstracts locating media by ID within a company's scope
 * and verifying media ownership.
 */
interface MediaManagerContract
{
    /**
     * Find a media record by its identifier that belongs to the given company.
     *
     * Should throw an exception if not found.
     */
    public function findForCompany(Company $company, int $mediaId): mixed;

    /**
     * Determine if the given media belongs to the company.
     */
    public function isOwnedByCompany(Company $company, mixed $media): bool;
}
