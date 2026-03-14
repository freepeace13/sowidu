<?php

namespace App\Transformers;

use App\Models\AddressRecord;
use App\Models\Employee;
use App\Models\User;
use App\Services\MediaFileService;
use App\Support\Facades\Impersonate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Packages\MediaLibrary\HasMedia;
use Packages\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property Media $resource
 *
 * @mixin Media
 */
class MediaTransformer extends Transformer
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'exists' => $this->exists,
            'is_dir' => $this->isFolder(),
            'metadata' => $this->metaAll(),
            'custom_properties' => $this->custom_properties,
            'category' => $this->resource?->category,
        ];

        if ($this->updated_at) {
            $data['modified'] = $this->updated_at->diffForHumans();
        }

        return $data['is_dir'] ? $data : array_merge($data, [
            'file_name' => $this->file_name,
            'extension' => $this->extension,
            'file_type' => $this->type,
            'mime_type' => $this->mime_type,
            'url' => $this->getUrl(),
            'size' => $this->getHumanReadableSizeAttribute(),
            'conversions' => $this->getConversionUrls(),
            'responsive_images' => [
                'urls' => $this->getResponsiveImageUrls(),
                'srcset' => $this->getSrcset(),
            ],
            'is_ready' => true,
        ]);
    }

    /**
     * @param  HasMedia  $model
     * @return self
     */
    public function withStarred($model)
    {
        return $this->state(function (array $attributes) use ($model) {
            return [
                'is_starred' => $this->isStarredBy($model),
            ];
        });
    }

    // public function withRootFolder()
    // {
    //     return $this->state(function (array $attributes) {
    //         $rootFolder = $this->getRootFolder();

    //         return [
    //             'root_folder' => [
    //                 'id' => optional($rootFolder)->id,
    //                 'uuid' => optional($rootFolder)->uuid,
    //                 'name' => optional($rootFolder)->name,
    //             ],
    //         ];
    //     });
    // }

    public function withOwner()
    {
        return $this->state(function (array $attributes) {
            $this->resource->loadMissing('model');

            return [
                'owner' => (new UserTransformer($this->resource->model))->resolve(),
            ];
        });
    }

    protected function authPermissions($mediaShare, User|Employee $member): array
    {
        $auth = Impersonate::user();

        $authIsMediaOwner = $this->resource->model->is($auth);
        $memberCanBeRemoved = false;

        // Auth is not impersonating
        if (!Impersonate::isImpersonating()) {
            $memberCanBeRemoved = !$authIsMediaOwner;
        } else {
            $memberCanBeRemoved = !$mediaShare->is_auto_shared;
        }

        return [
            'auth' => [
                'can_remove' => $memberCanBeRemoved,
            ],
        ];
    }

    public function withMembers()
    {
        return $this->state(function (array $attributes) {
            return [
                'members' => $this->resource
                    ->shares()
                    ->with(['person'])
                    ->get()
                    ->map(function ($mediaShare) {
                        $memberData = [];
                        $member = $mediaShare->person;

                        if (model_is($member, 'Employee')) {
                            $member->loadMissing(['user']);
                            $memberData = (new EmployeeTransformer($member))
                                ->withRoles()
                                ->withCompanyOwnerFlag()
                                ->withSharePermission($this->resource)
                                ->resolve();
                        }

                        return array_merge(
                            (new UserTransformer($member))
                                ->withSharePermission($this->resource)
                                ->resolve(),
                            $memberData,
                            $this->authPermissions($mediaShare, $member),
                        );
                    })
                    ->all(),
            ];
        });
    }

    /** @deprecated */
    // public function withFolder()
    // {
    //     return $this->state(function (array $attributes) {
    //         return [
    //             'folder' => [
    //                 'id' => $this->folder->id,
    //                 'uuid' => $this->folder->uuid,
    //                 'name' => $this->folder->name,
    //             ],
    //         ];
    //     });
    // }

    /**
     * @param  HasMedia  $model
     * @return self
     */
    public function withPolicies($model)
    {
        return $this->state(function (array $attributes) use ($model) {
            $gate = Gate::forUser($model);

            $canShare = $gate->allows('share', $this->resource);

            if (
                Impersonate::isImpersonating()
                && !morph_is($this->resource, \Sowidu\MediaManager\Models\MediaFolder::class) // TODO remove!
            ) {
                $media = $this->resource;
                $user = Impersonate::user();
                $company = Impersonate::tenant();
                $canShare = (new MediaFileService)->canShareMedia($media, $user, $company);
            }

            return [
                'policies' => [
                    'can_share' => $canShare,
                    'can_rename' => $gate->allows('rename', $this->resource),
                    'can_move' => $gate->allows('move', $this->resource),
                    'can_upload_file' => $gate->allows('upload-file', $this->resource),
                    'can_create_folder' => $gate->allows('create-folder', $this->resource),
                    'can_remove' => $gate->allows('remove', $this->resource),
                    'can_download' => $gate->allows('download', $this->resource),
                ],
            ];
        });
    }

    public function withIsShared($user)
    {
        return $this->state(fn ($attributes) => array_merge($attributes, [
            'is_shared' => $this->model->isNot($user),
        ]));
    }

    public function withAddressTag(?AddressRecord $address): self
    {
        return $this->state(fn ($attributes) => [
            'address_tag' => !$address ?: (new AddressRecordTransformer($address))->resolve(),
        ]);
    }

    public function withCreatedAt()
    {
        return $this->state(fn () => [
            'created_at' => $this->resource->created_at,
        ]);
    }

    public function withAttachedToOrders(Collection $orders)
    {
        return $this->state(
            fn () => [
                'attached_to_orders' => $orders->map(fn ($order) => $order->only(['id', 'order_number'])),
            ],
        );
    }
}
