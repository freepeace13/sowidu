<?php

namespace App\Http\Api\Resources\V1;

use App\Http\Api\Resources\V1\Teams\MemberRateResource;

class TeamMemberResource extends UserResource
{
    public function withTeamRate($team)
    {
        return $this->state(function () use ($team) {
            return [
                'rates' => $this->when(
                    $rate = $this->teamMembership($team)->rate,
                    fn () => new MemberRateResource($rate),
                ),
            ];
        });
    }

    public function withTeamRole($team)
    {
        return $this->state(function () use ($team) {
            return [
                'teamRole' => $this->teamMembership($team)->role,
            ];
        });
    }

    public function withMembershipId($team)
    {
        return $this->state(function ($attributes) use ($team) {
            return [
                'teamId' => $team->id,
                'membershipId' => $this->teamMembership($team)?->id,
            ];
        });
    }

    public function withIsOwner($team)
    {
        return $this->state(function ($attributes) use ($team) {
            return [
                'isOwner' => $this->ownsTeam($team),
            ];
        });
    }
}
