<?php

namespace App\Http\Api\Controllers\V1\Public;

use App\Models\LegalForm;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class GetLegalFormsController extends RestfulController
{
    public function __invoke(Request $request)
    {
        $legalForms = LegalForm::all();

        return $this->response([
            'legalForms' => $legalForms->map(fn (LegalForm $legalForm) => [
                'id' => $legalForm->id,
                'title' => $legalForm->legal_form,
                'abbrev' => $legalForm->abbreviation,
            ]),
        ]);
    }
}
