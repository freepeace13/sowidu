<?php

namespace App\Http\Controllers\Json\Organization;

use App\Http\Controllers\Json\BaseController;
use App\Models\Company as Organization;
use App\Transformers\Json\OrganizationTransformer;
use Illuminate\Http\Request;

class OrganizationController extends BaseController
{
    public function index(Request $request)
    {
        $result = Organization::search($request->keyword)->limit(5)->get();

        return $this->json(
            $result->map(fn ($resource) => (new OrganizationTransformer($resource))->resolve()),
        );
    }

    public function show($id)
    {
        $result = Organization::findOrFail($id);

        return $this->json(
            (new OrganizationTransformer($result))
                ->withAddress()
                ->resolve(),
        );
    }
}
