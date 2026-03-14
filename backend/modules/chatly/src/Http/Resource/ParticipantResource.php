<?php

namespace Modules\Chatly\Http\Resource;

use Modules\Chatly\Contracts\External\UrnResolverContract;
use Modules\Chatly\Contracts\External\UserDisplayContract;
use Packages\RestApi\Resources\JsonResource;

class ParticipantResource extends JsonResource
{
    protected static ?UrnResolverContract $urnResolver = null;

    protected static ?UserDisplayContract $userDisplay = null;

    /**
     * Set the URN resolver instance for all resources.
     */
    public static function setUrnResolver(UrnResolverContract $resolver): void
    {
        static::$urnResolver = $resolver;
    }

    /**
     * Set the user display instance for all resources.
     */
    public static function setUserDisplay(UserDisplayContract $display): void
    {
        static::$userDisplay = $display;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Resolve dependencies if not set
        if (static::$urnResolver === null) {
            static::$urnResolver = app(UrnResolverContract::class);
        }
        if (static::$userDisplay === null) {
            static::$userDisplay = app(UserDisplayContract::class);
        }

        $isTeamMember = static::$userDisplay->isTeamMember($this->messageable);
        $team = $isTeamMember ? $this->messageable->employer : null;

        $userId = $team ? $this->messageable->user->id : $this->messageable->id;
        $teamMembershipId = $team ? $this->messageable->id : null;
        $emailAddress = $team ? $this->messageable->user->email : $this->messageable->email;

        return [
            'id' => $this->id,
            'conversationId' => $this->conversation_id,
            'urn' => static::$urnResolver->generate($this->messageable),
            'userId' => $userId,
            'name' => static::$userDisplay->getDisplayName($this->messageable),
            'firstName' => $this->messageable->first_name,
            'lastName' => $this->messageable->last_name,
            'email' => $emailAddress,
            'teamId' => $team?->id,
            'teamMembershipId' => $teamMembershipId,
            'photo' => static::$userDisplay->getAvatarUrl($this->messageable),
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
