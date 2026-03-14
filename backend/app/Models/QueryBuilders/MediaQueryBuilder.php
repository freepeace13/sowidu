<?php

namespace App\Models\QueryBuilders;

use App\Services\MediaFileService;
use Illuminate\Database\Eloquent\Builder;

class MediaQueryBuilder extends Builder
{
    use CommonScopes\OwnerQueryScope;

    public function newest()
    {
        $this->query->orderBy('created_at', 'desc');

        return $this;
    }

    public function whereImages()
    {
        $this->query->whereIn('mimetype', MediaFileService::allowedMimetypes('images'));

        return $this;
    }

    public function whereVideos()
    {
        $this->query->whereIn('mimetype', MediaFileService::allowedMimetypes('videos'));

        return $this;
    }
}
