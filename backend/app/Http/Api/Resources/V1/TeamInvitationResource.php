<?php

namespace App\Http\Api\Resources\V1;

use App\Models\User;
use Packages\RestApi\Resources\JsonResource;

/**
 * @property \App\Models\Invitation $resource
 */
class TeamInvitationResource extends JsonResource
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
            'teamId' => $this->company_id,
            'email' => $this->email,
            'sentDate' => $this->created_at->toISOString(),

            'isAccepted' => filled($this->accepted_at),
            'isPending' => blank($this->accepted_at) && !$this->declined,
            'isDeclined' => $this->declined,
        ];
    }

    public function withAcceptedDate()
    {
        return $this->state(function ($data) {
            return [
                'acceptedAt' => $this->accepted_at?->toISOString(),
            ];
        });
    }

    public function withPhoto()
    {
        return $this->state(function ($data) {
            $user = User::whereEmail($this->email)->first();

            return [
                'photo' => $user ? get_user_avatar_url($user) : null,
            ];
        });
    }
}
