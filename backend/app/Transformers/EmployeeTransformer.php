<?php

namespace App\Transformers;

use App\Enums\UserType;
use App\Models\EmployeeRate;
use Illuminate\Support\Facades\Gate;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class EmployeeTransformer extends Transformer
{
    use Traits\WithUrnAttribute;

    public function toArray($request)
    {
        $this->loadMissing(['employer']);

        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'photo' => get_user_avatar_url($this->user),
            'email' => $this->email,
            'is_owner' => $this->employer->user->is($this->user),
            'user_type' => array_flip(UserType::getConstants())[get_class($this->resource)],
        ];
    }

    public function withUserDetails()
    {
        return $this->state(function (array $attributes) {
            $user = $this->user;

            return [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
            ];
        });
    }

    public function withCompany()
    {
        return $this->state(function (array $attributes) {
            $employer = $this->employer;

            return [
                'company' => [
                    'id' => $employer->id,
                    'name' => $employer->name,
                    'user_id' => $employer->user_id,
                ],
            ];
        });
    }

    public function withRoles()
    {
        $this->loadMissing(['roles']);

        return $this->state(function (array $attributes) {
            return [
                'roles' => $this->roles->pluck('name'),
            ];
        });
    }

    public function withSharePermission(Media $media)
    {
        return $this->state(function (array $attributes) use ($media) {
            Gate::forUser($this->resource);

            $sharing = $media->shares()
                ->whereAccount($this->resource)
                ->first();

            return [
                'sharing' => [
                    'permission' => optional($sharing)->permission,
                    'can' => [
                        'write' => $media->isWriteableFor($this->resource),
                        'read' => $media->isReadableFor($this->resource),
                    ],
                ],
            ];
        });
    }

    public function withCompanyOwnerFlag()
    {
        return $this->state(function (array $attributes) {
            return array_merge($attributes, [
                'is_company_owner' => $this->employer->user->is($this->user),
            ]);
        });
    }

    public function withRate(?EmployeeRate $employeeRate = null): self
    {
        return $this->state(fn () => [
            'rate' => $employeeRate ? EmployeeRateTransformer::make($employeeRate) : null,
        ]);
    }
}
