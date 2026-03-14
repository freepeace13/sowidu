<?php

namespace App\Models;

use App\Models\Relations\AuthoredByUser;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class Attachment extends Model
{
    use AuthoredByUser;
    use HasUuids;

    protected $fillable = [
        'media_file_id',
        'user_id',
    ];

    /** @return BelongsTo|Media */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_file_id');
    }

    public function model()
    {
        return $this->morphTo();
    }
}
