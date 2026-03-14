<?php

namespace App\Services;

use Packages\MediaLibrary\HasMedia;
use Packages\MediaLibrary\MediaCollections\Models\MediaShare;

class FileSharingService
{
    protected $fileService;

    protected $media;

    public function __construct(MediaFileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function setMedia($media)
    {
        if (is_string($media)) {
            $media = $this->fileService->findByUuidOrFail($media);
        }

        $this->media = $media;

        return $this;
    }

    public function getMedia()
    {
        return $this->media;
    }

    public function isBelongsTo(HasMedia $user)
    {
        return $this->media->isOwnedBy($user);
    }

    public function isAccessibleTo(HasMedia $user)
    {
        return $this->media->isOwnedBy($user) || $this->media->wasSharedTo($user);
    }

    public function getAllUsers()
    {
        return $this->getSharedUsers()->prepend($this->media->model);
    }

    public function getSharedUsers()
    {
        return $this->media->getMembers();
    }

    public function unshareFrom(HasMedia $user)
    {
        $this->media->unshareFrom($user);
    }

    public function shareTo(HasMedia $user, $permission = MediaShare::READONLY)
    {
        $this->media->shareTo($user, $permission);
    }

    public function getUsersPermissions(HasMedia $user)
    {
        if ($this->isBelongsTo($user)) {
            return MediaShare::READWRITE;
        }

        $sharing = $this->media->shares()
            ->whereAccount($user)
            ->first();

        return $sharing?->permission;
    }

    public function canReadFile(HasMedia $user)
    {
        if ($this->isBelongsTo($user)) {
            return true;
        }

        return $this->media->isReadableFor($user);
    }

    public function canWriteFile(HasMedia $user)
    {
        if ($this->isBelongsTo($user)) {
            return true;
        }

        return $this->media->isWriteableFor($user);
    }
}
