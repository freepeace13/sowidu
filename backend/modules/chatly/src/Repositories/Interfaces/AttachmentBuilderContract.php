<?php

namespace Modules\Chatly\Repositories\Interfaces;

use Illuminate\Http\UploadedFile;

interface AttachmentBuilderContract
{
    public function build($payload);

    public function conversation($conversationId);

    public function fromFile(UploadedFile $file);

    public function fromMedia(int $mediaId);
}
