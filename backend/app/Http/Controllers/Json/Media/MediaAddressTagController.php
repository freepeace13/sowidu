<?php

namespace App\Http\Controllers\Json\Media;

use App\Http\Controllers\Json\BaseController;
use App\Services\MediaFileService;
use App\Transformers\AddressRecordTransformer;

class MediaAddressTagController extends BaseController
{
    public function __invoke(string $media, MediaFileService $service)
    {
        $media = $service->select('id')->findByUuid($media);
        $address = $media->addressTags()->first();

        return $this->json(!$address ?: (new AddressRecordTransformer($address))
            ->resolve());
    }
}
