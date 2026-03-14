<?php

namespace Packages\MediaLibrary\MediaCollections\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Packages\MediaLibrary\HasMedia;

class MediaShare extends Model
{
    const READONLY = 'r';
    const READWRITE = 'rw';

    protected $table = 'media_shares';

    protected $guarded = [];

    protected $casts = [
        'is_auto_shared' => 'boolean',
    ];

    public function scopeWhereWriteableFor(Builder $query, HasMedia $model)
    {
        return $query
            ->whereAccount($model)
            ->whereCanReadOrWrite();
    }

    public function scopeWhereReadableFor(Builder $query, HasMedia $model)
    {
        return $query
            ->whereAccount($model)
            ->where(function ($query) {
                $query
                    ->whereCanReadOnly()->orWhere
                    ->whereCanReadOrWrite();
            });
    }

    public function scopeWhereCanReadOnly(Builder $query)
    {
        return $query->where('media_shares.permission', static::READONLY);
    }

    public function scopeWhereCanReadOrWrite(Builder $query)
    {
        return $query->where('media_shares.permission', static::READWRITE);
    }

    public function scopeWhereAccount(Builder $query, HasMedia $model)
    {
        return $query->where(function ($query) use ($model) {
            $query
                ->where('media_shares.account_id', $model->getKey())
                ->where('media_shares.account_type', $model->getMorphClass());
        });
    }

    public function scopeWhereMedia(Builder $query, Media $media)
    {
        return $query->where(function ($query) use ($media) {
            $query
                ->where('media_shares.shareable_id', $media->getKey())
                ->where('media_shares.shareable_type', $media->getMorphClass());
        });
    }

    public function person()
    {
        return $this->morphTo(__FUNCTION__, 'account_type', 'account_id');
    }
}
