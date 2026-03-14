<?php

declare(strict_types=1);

namespace App\Services\Offer;

use App\Models\Company;
use App\Transformers\CompanyTransformer;
use Illuminate\Database\Eloquent\Model;
use Modules\Offer\Contracts\External\CompanyServiceContract;

class CompanyServiceAdapter implements CompanyServiceContract
{
    public function find(int $id): ?Model
    {
        return Company::find($id);
    }

    public function findOrFail(int $id): Model
    {
        return Company::findOrFail($id);
    }

    public function transformWithFullDetails(Model $company): array
    {
        /** @var Company $company */
        return (new CompanyTransformer($company))
            ->withTaxSettings()
            ->withInvoiceDefaults()
            ->withCurrentAddress($company->currentPlace()->first())
            ->withType()
            ->resolve();
    }

    public function transformForRecipient(Model $company): array
    {
        /** @var Company $company */
        return (new CompanyTransformer($company))
            ->withCurrentAddress($company->currentPlace)
            ->withCompanyOwnerDetails($company->user)
            ->withType()
            ->resolve();
    }

    public function getCurrentPlace(Model $company): ?Model
    {
        /** @var Company $company */
        return $company->currentPlace;
    }
}
