<?php

namespace Packages\MediaLibrary\MediaCollections\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Packages\MediaLibrary\HasMedia;
use Packages\MediaLibrary\MediaCollections\Models\MediaShare;

trait Shareable
{
    // public function isShared()
    // {
    //     return $this->shares()->exists();
    // }

    // public function isShareable()
    // {
    //     return $this->exists && is_null($this->folder_id);
    // }

    public function wasntShared()
    {
        return !$this->wasShared();
    }

    public function wasShared()
    {
        return $this->shares()->count() > 0;
    }

    public function wasSharedTo(HasMedia $model)
    {
        return $this->shares()->whereAccount($model)->exists();
    }

    public function isOwnedBy(HasMedia $model)
    {
        return $this->model_id === $model->getKey()
            && $this->model_type === $model->getMorphClass();
    }

    public function isWriteableFor(HasMedia $model)
    {
        return $this->shares()->whereWriteableFor($model)->exists();
    }

    public function isReadableFor(HasMedia $model)
    {
        return $this->shares()->whereReadableFor($model)->exists();
    }

    public function shareTo(Model $account, $permission = MediaShare::READONLY)
    {
        $this->shares()
            ->updateOrCreate([
                'account_id' => $account->getKey(),
                'account_type' => $account->getMorphClass(),
            ], [
                'permission' => $permission,
            ]);
    }

    public function autoShareTo(Model $account, $permission = MediaShare::READONLY)
    {
        $this->shares()
            ->updateOrCreate([
                'account_id' => $account->getKey(),
                'account_type' => $account->getMorphClass(),
            ], [
                'permission' => $permission,
                'is_auto_shared' => true,
            ]);
    }

    public function unshareFrom(HasMedia $account)
    {
        $this->shares()
            ->whereAccount($account)
            ->delete();
    }

    public function unshare()
    {
        $this->shares()->delete();
    }

    public function getMembers()
    {
        return $this->shares()->with('person')->get()->map->person;
    }

    public function shares()
    {
        return $this->morphMany(MediaShare::class, 'shareable');
    }
}
