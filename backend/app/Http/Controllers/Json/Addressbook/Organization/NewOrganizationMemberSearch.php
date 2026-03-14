<?php

namespace App\Http\Controllers\Json\Addressbook\Organization;

use App\Http\Controllers\Json\Addressbook\BaseController;
use App\Transformers\Json\AddressbookTransformer;
use Illuminate\Http\Request;

class NewOrganizationMemberSearch extends BaseController
{
    public function __invoke(Request $request, $id)
    {
        $text = $request->query('text');
        $size = $request->query('size', 10);

        $service = $this->createServiceInstance();

        $organization = $service->organizations()->find($id);

        if (!$organization) {
            abort(404, 'Addressbook not found.');
        }

        $memberIds = $organization->organizationMembers->pluck('id')->all();

        return AddressbookTransformer::collection(
            $service->people()
                ->whereNotIn('id', $memberIds)
                ->matchesText($text)
                ->limit($size)
                ->get(),
        );
    }
}
