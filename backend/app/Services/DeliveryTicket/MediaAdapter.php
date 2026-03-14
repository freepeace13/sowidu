<?php

namespace App\Services\DeliveryTicket;

use App\Services\MediaFileService;
use Illuminate\Http\UploadedFile;
use Modules\DeliveryTicket\Contracts\External\MediaContract;

class MediaAdapter implements MediaContract
{
    public function uploadDocument(UploadedFile $file, mixed $model, string $collection = 'documents'): mixed
    {
        return MediaFileService::upload($file, $model, $collection);
    }

    public function deleteDocument(mixed $media): void
    {
        $media->delete();
    }

    public function getDocuments(mixed $model, string $collection = 'documents'): mixed
    {
        return $model->getMedia($collection);
    }

    public function getAllowedMimetypes(): array
    {
        return MediaFileService::allowedMimetypes();
    }
}
