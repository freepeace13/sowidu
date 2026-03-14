<?php

namespace App\Support\Organization;

use App\Models\Company;

class OrganizationSettings
{
    public function __construct(protected Company $company)
    {
        //
    }

    public function media(): OrganizationMediaSettings
    {
        return new OrganizationMediaSettings($this->company);
    }

    public function invoiceDefaults(): OrganizationInvoiceDefaults
    {
        return new OrganizationInvoiceDefaults($this->company);
    }

    public function saveDefaults()
    {
        $this->media()
            ->saveDefaults();
    }
}
