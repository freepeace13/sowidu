<?php

namespace App\Http\Controllers\Json\Addressbook\Organization;

use App\Http\Controllers\Json\Addressbook\BaseController;
use App\Models\Company as Organization;
use App\Transformers\Json\OrganizationTransformer;
use Illuminate\Http\Request;

class NewOrganizationSearch extends BaseController
{
    public function __invoke(Request $request)
    {
        $text = $request->query('text');
        $size = $request->query('size', 10);

        $organizations = $this->createServiceInstance()->getOrganizationIds();
        $excludeIds = $organizations
            ->when(
                $this->isImpersonating(),
                fn ($collection) => $collection->push($this->getCurrentTeamId()),
            )
            ->toArray();

        return OrganizationTransformer::collection(
            Organization::search($text)
                ->whereNotIn('id', $excludeIds)
                ->limit($size)
                ->get(),
        );
    }
}
