<?php

namespace Modules\Chatly\Http\Resource;

use Packages\RestApi\Resources\JsonResource;

class MessageResource extends JsonResource
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
            'body' => $this->body,
            'type' => $this->type, // text or attachment
            'data' => $this->data,
            'participantId' => $this->participation_id,
            'conversationId' => $this->conversation_id,
            'sender' => new ParticipantResource($this->participation),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
