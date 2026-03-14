<?php

namespace App\Http\Api\Controllers\V1\Public;

use App\Models\InstitutionType;
use Packages\RestApi\RestfulController;

class GetInstitutionTypesController extends RestfulController
{
    public function __invoke()
    {
        $institutionTypes = InstitutionType::all();

        return $this->response([
            'institutions' => $institutionTypes->map(fn (InstitutionType $type) => [
                'id' => $type->id,
                'title' => $type->type,
                'abbrev' => $type->abbreviation,
            ]),
        ]);
    }
}
