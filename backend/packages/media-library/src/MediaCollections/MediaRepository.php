<?php

namespace Packages\MediaLibrary\MediaCollections;

use Packages\MediaLibrary\MediaCollections\Models\Media;

class MediaRepository
{
    protected $subject;

    protected $model;

    public function __construct(Media $model)
    {
        $this->model = $model;
    }

    public function isWriteable(Media $media)
    {
        return $media->shares()
            ->whereAccount($this->subject)
            ->whereCanReadOrWrite()
            ->exists();
    }

    public function isReadable(Media $media)
    {
        return $media->shares()
            ->whereAccount($this->subject)
            ->whereCanReadOnly()->orWhere
            ->whereCanReadOrWrite()
            ->exists();
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    protected function newQuery($depth = 0)
    {
        return $this->subject
            ? $this->makeQueryWithSubject($depth)
            : $this->makeQueryWithoutSubject();
    }

    protected function makeQueryWithoutSubject()
    {
        return $this->model->query();
    }

    protected function makeQueryWithSubject($depth = 0)
    {
        return $this->model->query()->where(fn ($query) => $query
            ->whereHas('shares', function ($query) {
                $query->whereReadableFor($this->subject);
            })->orWhere(function ($query) {
                $query->where('model_id', $this->subject->getKey())
                    ->where('model_type', $this->subject->getMorphClass());
            }));
    }

    public function getRootFiles($filters = [])
    {
        return $this->newQuery()
            ->filter($filters)
            ->whereNull('folder_id')
            ->get();
    }

    public function getPaginatedRootFiles($filters = [])
    {
        return $this->newQuery()
            ->filter($filters)
            ->whereNull('folder_id')
            ->latest()
            ->paginate();
    }
}
