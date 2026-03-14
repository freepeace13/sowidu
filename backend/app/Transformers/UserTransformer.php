<?php

namespace App\Transformers;

use App\Enums\UserType;
use Illuminate\Support\Facades\Gate;
use Packages\Addressable\Models\Address;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class UserTransformer extends Transformer
{
    use Traits\WithUrnAttribute;

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->full_name,
            'email' => $this->resource->email,
            'photo' => get_user_avatar_url($this->resource),
            'user_type' => array_flip(UserType::getConstants())[get_class($this->resource)],
        ];
    }

    public function withAliasName($auth)
    {
        return $this->state(fn ($attrs) => [
            'alias_name' => $this->resource->is($auth) ? 'You' : null,
        ]);
    }

    public function withSharePermission(Media $media)
    {
        return $this->state(function (array $attributes) use ($media) {
            $gate = Gate::forUser($this->resource);

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

    public function withAddress()
    {
        $address = $this->resource->address()
            ->newestFirst() ?? new Address;

        return $this->state(function ($attributes) use ($address) {
            return [
                'address' => (new AddressTransformer($address))->resolve(),
            ];
        });
    }
}
