<?php

namespace App\Http\Api\Resources\V1\Media;

use App\Services\FileSharingService;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;
use Packages\RestApi\Resources\JsonResource;
use Packages\Urn\UrnManager;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'urn' => UrnManager::generate($this->resource),
            'name' => $this->full_name,
            'email' => $this->email,
            'photo' => get_user_avatar_url($this->resource->user ?? $this->resource),
        ];
    }

    public function withIsOwner(MediaFile $media)
    {
        return $this->state(fn ($attributes) => array_merge($attributes, [
            'isOwner' => app(FileSharingService::class)
                ->setMedia($media)
                ->isBelongsTo($this->resource),
        ]));
    }

    public function withPermissions(MediaFile $media)
    {
        $fileSharingService = app(FileSharingService::class)->setMedia($media);

        return $this->state(fn ($attributes) => array_merge($attributes, [
            'scopes' => $fileSharingService->getUsersPermissions($this->resource),
            'canRead' => $fileSharingService->canReadFile($this->resource),
            'canWrite' => $fileSharingService->canWriteFile($this->resource),
        ]));
    }
}
