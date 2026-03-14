<?php

namespace App\Policies;

use App\Models\Company as Team;
use App\Models\User;
use App\Policies\Traits\HandlesTeamAuthorization;
use Illuminate\Auth\Access\HandlesAuthorization;
use Packages\MediaLibrary\HasMedia;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class MediaPolicy
{
    use HandlesAuthorization, HandlesTeamAuthorization;

    public function store(User $user, ?Team $team = null)
    {
        if (is_null($team)) {
            return true;
        }

        return $user->belongsToTeam($team);
    }

    public function download(HasMedia $user, Media $media)
    {
        return $media->exists && ($media->isOwnedBy($user) || $media->isReadableFor($user));
    }

    public function forceDelete(HasMedia $user, Media $media)
    {
        return $media->exists && $media->isOwnedBy($user);
    }

    public function remove(HasMedia $user, Media $media)
    {
        return $media->exists && ($media->isOwnedBy($user) || $media->wasSharedTo($user));
    }

    public function modifyMembers(HasMedia $user, Media $media)
    {
        return $media->exists
            && ($media->isOwnedBy($user) || $media->isWriteableFor($user));
    }

    public function modifyPermission(HasMedia $user, Media $media)
    {
        return ($media->exists && $media->wasShared())
            && ($media->isOwnedBy($user) || $media->isWriteableFor($user));
    }

    public function uploadFile(HasMedia $user, Media $media)
    {
        if (!$media->exists) {
            return true;
        }

        return $media->isFolder()
            && ($media->isOwnedBy($user) || $media->isWriteableFor($user));
    }

    public function createFolder(HasMedia $user, Media $media)
    {
        if (!$media->exists) {
            return false;
        }
    }

    public function view(HasMedia $user, Media $media)
    {
        if ($media->exists && ($media->isOwnedBy($user) || $media->isReadableFor($user))) {
            return true;
        }
    }

    public function share(HasMedia $user, Media $media)
    {
        return $media->exists
            && $media->isRoot()
            && ($media->isOwnedBy($user) || $media->isWriteableFor($user));
    }

    public function rename(HasMedia $user, Media $media)
    {
        if (!$media->exists) {
            return false;
        }

        return $media->isOwnedBy($user)
            || $media->isWriteableFor($user);
    }

    public function move(HasMedia $user, Media $media)
    {
        if (!$media->exists) {
            return false;
        }
    }
}
