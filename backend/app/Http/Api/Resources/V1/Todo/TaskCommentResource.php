<?php

namespace App\Http\Api\Resources\V1\Todo;

use Packages\RestApi\Resources\JsonResource;

class TaskCommentResource extends JsonResource
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
            'taskId' => $this->task_id,
            'authorId' => $this->author_id,
            'message' => $this->message,
            'createdAt' => $this->created_at,
        ];
    }
}
