<?php

namespace App\Http\Api\Resources\V1\Media;

use App\Services\FileSharingService;
use Illuminate\Support\Facades\Gate;
use Packages\MediaLibrary\HasMedia;
use Packages\RestApi\Resources\JsonResource;
use Packages\Urn\UrnManager;

/** @extends \Packages\MediaLibrary\MediaCollections\Models\Media */
class MediaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->uuid,
            'title' => $this->name,
            'file' => [
                'name' => $this->file_name,
                'type' => $this->type,
                'size' => $this->size,
                'uri' => $this->getUrl(),
                'thumbnail' => $this->getUrl('thumbnail'),
            ],
            'shared' => $this->wasShared(),
            'uploadDate' => $this->created_at->toW3cString(),
        ];
    }

    public function withOwner()
    {
        return $this->state(fn () => [
            'owner' => [
                'id' => $this->model->getKey(),
                'urn' => UrnManager::generate($this->model),
                'name' => $this->model->full_name,
                'photo' => get_user_avatar_url($this->model->user ?? $this->model),
            ],
        ]);
    }

    public function withResponsiveImages()
    {
        return $this->state(fn ($attributes) => [
            'file' => array_merge($attributes['file'], [
                'responsive' => $this->responsiveImages()->getSource(),
            ]),
        ]);
    }

    public function withPolicy(HasMedia $user)
    {
        $gate = Gate::forUser($user);

        return $this->state(fn () => [
            'policy' => [
                'canOpen' => app(FileSharingService::class)
                    ->setMedia($this->resource)
                    ->isAccessibleTo($user),
                'canShare' => $gate->allows('share', $this->resource),
                'canDownload' => $gate->allows('download', $this->resource),
                'canRename' => $gate->allows('rename', $this->resource),
                'canDelete' => $gate->allows('remove', $this->resource),
            ],
        ]);
    }
}
