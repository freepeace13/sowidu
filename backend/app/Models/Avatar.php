<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    public $increments = false;

    const CREATED_AT = null;

    protected $fillable = [
        'ownerable_id',
        'ownerable_type',
        'media_id',
    ];

    public function owner()
    {
        return $this->morphTo();
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('ownerable_id', $this->getAttribute('ownerable_id'))
            ->where('ownerable_type', $this->getAttribute('ownerable_type'));

        return $query;
    }
}
