<?php

namespace Modules\Chatly\Repositories\Traits;

trait ChatAttachmentTrait
{
    /**
     * Identify attachment type
     *
     * @param  string  $mime
     * @return string
     */
    public function attachmentType($mime)
    {
        switch ($mime) {
            case str_contains($mime, 'image'):
                $type = 'image';
                break;
            case str_contains($mime, 'pdf'):
                $type = 'pdf';
                break;
            case str_contains($mime, 'video'):
                $type = 'video';
                break;
            default:
                $type = 'text';
                break;
        }

        return $type;
    }
}
