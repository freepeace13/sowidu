<?php

namespace App\Events\Media;

use App\Support\Models\InteractsWithModelChanges;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class MediaTaggedWithCategory
{
    use Dispatchable, InteractsWithModelChanges, InteractsWithSockets, SerializesModels;

    public function __construct(public Media $mediaFile, public array $changes = [])
    {
        $this->changes = $changes ?: $mediaFile->getChanges();
    }
}
