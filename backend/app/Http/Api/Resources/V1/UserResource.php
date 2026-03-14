<?php

namespace App\Http\Api\Resources\V1;

use Packages\RestApi\Resources\JsonResource;
use Packages\Urn\UrnManager;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'urn' => UrnManager::generate($this->resource),
            'email' => $this->email,
            'fullName' => $this->full_name,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'birthdate' => $this->profile->birthdate,
            'gender' => $this->profile->gender,
            'photo' => get_user_avatar_url($this->resource),
        ];
    }

    public function withTeams()
    {
        return $this->state(function ($attributes) {
            return [
                'teams' => TeamResource::collection($this->teams),
            ];
        });
    }

    public function withCurrentTeam($team = null)
    {
        $team = $team ?? $this->resource?->currentTeam();

        return $this->state(function () use ($team) {
            return [
                'currentTeam' => $this->when($team, function () use ($team) {
                    return (new TeamResource($team))
                        ->withMembershipId($this->resource)
                        ->resolve();
                }, null),
            ];
        });
    }

    public function withRoles($team = null)
    {
        $team = $team ?? $this->resource?->currentTeam();

        return $this->state(function ($attributes) use ($team) {
            return [
                'roles' => $this->teamRoles($team),
            ];
        });
    }

    public function withPermissions($team = null)
    {
        $team = $team ?? $this->resource?->currentTeam();

        return $this->state(function ($attributes) use ($team) {
            return [
                'permissions' => $this->teamPermissions($team),
            ];
        });
    }
}
