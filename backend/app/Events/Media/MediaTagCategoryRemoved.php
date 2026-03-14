<?php

namespace App\Events\Media;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class MediaTagCategoryRemoved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Media $mediaFile)
    {
        //
    }
}
