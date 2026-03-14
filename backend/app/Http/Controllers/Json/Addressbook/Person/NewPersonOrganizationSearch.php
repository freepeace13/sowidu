<?php

namespace App\Http\Controllers\Json\Addressbook\Person;

use App\Http\Controllers\Json\Addressbook\BaseController;
use App\Transformers\Json\AddressbookTransformer;
use Illuminate\Http\Request;

class NewPersonOrganizationSearch extends BaseController
{
    public function __invoke(Request $request, $id)
    {
        $text = $request->query('text');
        $size = $request->query('size', 10);

        $service = $this->createServiceInstance();

        if (!$person = $service->people()->find($id)) {
            abort(404, 'Addressbook not found.');
        }

        return AddressbookTransformer::collection(
            $service->organizations()
                ->matchesText($text)
                ->doesntHaveMembers($person->id)
                ->limit($size)
                ->get(),
        );
    }
}
