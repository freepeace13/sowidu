<?php

namespace App\Http\Api\Resources\V1\Todo;

use Packages\RestApi\Resources\JsonResource;

class TaskResource extends JsonResource
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
            'boardId' => $this->board_id,
            'group' => $this->group,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    public function withReporter()
    {
        return $this->state(function ($attributes) {
            return [
                'reporter' => new SubscriberResource($this->reporter),
            ];
        });
    }

    public function withMembers()
    {
        return $this->state(function ($attributes) {
            return [
                'members' => SubscriberResource::collection($this->members),
            ];
        });
    }
}
