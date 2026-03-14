<?php

namespace App\Http\Api\Actions\Teams;

use App\Contracts\Actions\UpdatesTeamProfile;
use App\Events\OrganizationProfileUpdated;
use App\Models\Company as Team;
use Packages\RestApi\RestApiAction;

class UpdateTeamProfile extends RestApiAction implements UpdatesTeamProfile
{
    public function getValidationRules(): array
    {
        return [
            'name' => ['required', 'string'],
            'legalForm' => ['required', 'integer', 'exists:legal_forms,id'],
            'institutionType' => ['required', 'integer', 'exists:institution_types,id'],
        ];
    }

    public function update(Team $team, array $data, $errorBag = null): Team
    {
        $validated = $this->validate($data, $errorBag);

        $team->update([
            'name' => $validated['name'],
            'legal_form_id' => $validated['legalForm'],
            'institution_type_id' => $validated['institutionType'],
        ]);

        $team->refresh();

        event(new OrganizationProfileUpdated($team));

        return $team;
    }
}
