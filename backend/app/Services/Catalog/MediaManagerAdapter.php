<?php

namespace App\Services\Catalog;

use App\Models\Company;
use App\Services\MediaFileService;
use Modules\Catalog\Contracts\External\MediaManagerContract;

class MediaManagerAdapter implements MediaManagerContract
{
    public function findForCompany(Company $company, int $mediaId): mixed
    {
        return MediaFileService::makeForCompany($company)->findOrFail($mediaId);
    }

    public function isOwnedByCompany(Company $company, mixed $media): bool
    {
        return MediaFileService::companyOwned($company, $media);
    }
}
