<?php

namespace App\Http\Api\Actions\Teams;

use App\Contracts\Actions\CreatesTeams;
use App\Events\NewCompanyRegistered;
use App\Models\Company as Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Packages\RestApi\RestApiAction;

class CreateTeam extends RestApiAction implements CreatesTeams
{
    protected $rules = [
        'name' => ['required', 'string', 'min:5', 'unique:companies'],
        'institutionTypeId' => ['required', 'exists:institution_types,id'],
        'legalFormId' => ['required', 'exists:legal_forms,id'],
    ];

    public function create(User $user, array $data, $errorBag = null): Team
    {
        $validated = $this->validate($data, $errorBag);

        $newTeam = DB::transaction(fn () => Team::create([
            'name' => $validated['name'],
            'user_id' => $user->getKey(),
            'institution_type_id' => $validated['institutionTypeId'],
            'legal_form_id' => $validated['legalFormId'],
            'confirmed' => true,
        ]));

        event(new NewCompanyRegistered($newTeam));

        return $newTeam;
    }
}
