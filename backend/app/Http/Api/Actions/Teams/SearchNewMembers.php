<?php

namespace App\Http\Api\Actions\Teams;

use App\Models\Company as Team;
use App\Models\User;
use Illuminate\Http\Response;
use Packages\RestApi\RestApiAction;

class SearchNewMembers extends RestApiAction
{
    public function search(
        User $user,
        Team $team,
        $keyword = '',
        int $limit = 3,
        $errorBag = null,
    ) {
        abort_unless($user->belongsToTeam($team), Response::HTTP_FORBIDDEN);

        $results = User::query()
            ->when(filled($keyword), fn ($query) => $query->search($keyword))
            ->whereNotIn('id', function ($query) use ($team) {
                $query->select('user_id')
                    ->from('employees')
                    ->where('company_id', $team->id);
            })
            ->whereNotIn('email', function ($query) use ($team) {
                $query->select('email')
                    ->from('company_invites')
                    ->where('company_id', $team->id);
            })
            ->limit($limit)
            ->get();

        return $results;
    }
}
