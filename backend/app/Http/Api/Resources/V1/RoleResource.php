<?php

namespace App\Http\Api\Resources\V1;

use App\Models\Permission;
use Packages\RestApi\Resources\JsonResource;

class RoleResource extends JsonResource
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
            'name' => $this->name,
        ];
    }

    public function withPermissions()
    {
        return $this->state(function ($data) {
            return [
                'permissions' => Permission::all()
                    ->map(fn ($permission) => [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'hasDirectPermission' => $this->hasDirectPermission($permission),
                    ])
                    ->toArray(),
            ];
        });
    }
}
