<?php

namespace App\Http\Api\Resources\V1\Chat;

use Packages\RestApi\Resources\JsonResource;
use Packages\Urn\UrnManager;

class ParticipantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $team = $this->messageable->employer;

        $userId = $team ? $this->messageable->user->id : $this->messageable->id;
        $teamMembershipId = $team ? $this->messageable->id : null;
        $emailAddress = $team ? $this->messageable->user->email : $this->messageable->email;

        return [
            'id' => $this->id,
            'conversationId' => $this->conversation_id,
            'urn' => UrnManager::generate($this->messageable),
            'userId' => $userId,
            'name' => $this->messageable->full_name,
            'firstName' => $this->messageable->first_name,
            'lastName' => $this->messageable->last_name,
            'email' => $emailAddress,
            'teamId' => $team?->id,
            'teamMembershipId' => $teamMembershipId,
            'photo' => get_user_avatar_url($this->messageable),
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
