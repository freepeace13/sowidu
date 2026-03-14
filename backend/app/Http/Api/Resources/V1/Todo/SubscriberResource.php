<?php

namespace App\Http\Api\Resources\V1\Todo;

use App\Http\Api\Resources\V1\UserResource;
use Packages\RestApi\Resources\JsonResource;

class SubscriberResource extends JsonResource
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
            'role' => $this->role,
            'user' => new UserResource($this->user),
            'settings' => $this->settings ?? [],
        ];
    }
}
