<?php

namespace App\Http\Api\Controllers\V1\Teams;

use App\Contracts\Actions\CreatesTeams;
use App\Http\Api\Resources\V1\TeamResource;
use App\Models\InstitutionType;
use App\Models\LegalForm;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class CreateTeamController extends RestfulController
{
    public function __invoke(Request $request, CreatesTeams $creator)
    {
        $user = $this->currentUser();

        $newTeam = $creator->create($user, $request->all());

        return new TeamResource($newTeam);
    }

    public function institutions()
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

    public function legalForms()
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
