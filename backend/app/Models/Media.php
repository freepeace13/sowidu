<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @todo Remove this class
 *
 * @deprecated v2.x \Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile instead
 * @see \Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile
 */
class Media extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ownerable_id',
        'ownerable_type',
        'name',
        'description',
        'filename',
        'mimetype',
        'type',
        'size',
    ];

    /**
     *  Setup model event hooks
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            if (!$model->name) {
                $model->name = 'Give me a name';
            }

            if (!$model->description) {
                $model->description = 'Tell something about me...';
            }
        });
    }

    /**
     * Get the URL attribute of the media
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return \Storage::disk('media-old')->url($this->filename);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new QueryBuilders\MediaQueryBuilder($query);
    }

    /**
     * Get the owner of the media
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('ownerable');
    }
}
