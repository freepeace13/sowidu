<?php

namespace App\Http\Api\Resources\V1\Chat;

use Illuminate\Support\Arr;
use Packages\RestApi\Resources\JsonResource;

class ConversationResource extends JsonResource
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
            'name' => Arr::get($this->data, 'name'),
            'photo' => Arr::get($this->data, 'avatar'),
            'directMessage' => $this->direct_message,
            'private' => $this->private,
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }

    public function withParticipants()
    {
        return $this->state(function ($attributes) {
            return [
                'participants' => ParticipantResource::collection($this->participants),
            ];
        });
    }

    public function withLastMessage()
    {
        return $this->state(function (array $attributes) {
            return [
                'lastMessage' => $this->last_message
                    ? (new MessageResource($this->last_message))->resolve()
                    : null,
            ];
        });
    }
}
