<?php

namespace App\Models;

use App\Enums\AttachmentType;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class OrderAttachment extends Pivot
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => AttachmentType::class,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function media()
    {
        return $this->belongsTo(MediaFile::class);
    }

    public function user()
    {
        return $this->belongsTo(MediaFile::class);
    }
}
