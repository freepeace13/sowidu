<?php

namespace App\Http\Api\Resources\V1\Todo;

use Packages\RestApi\Resources\JsonResource;

class BoardResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'groups' => $this->settings()->groups()->all(),
        ];
    }

    public function withSubscribers()
    {
        return $this->state(function ($attributes) {
            return [
                'subscribers' => SubscriberResource::collection($this->subscribers),
            ];
        });
    }
}
