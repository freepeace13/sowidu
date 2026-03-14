<?php

namespace Modules\Shared\Models\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Shared\Enums\AttachmentType;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

trait HasMediaAttachments
{
    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(
            MediaFile::class,
            'order_attachment',
            'order_id',
            'media_file_id',
            'id',
            'id',
        )
            ->withPivot('type')
            ->withTimestamps();
    }

    public function medias()
    {
        return $this->attachments()
            ->wherePivot('type', AttachmentType::MEDIA);
    }

    public function documents(): BelongsToMany
    {
        return $this->attachments()
            ->wherePivot('type', AttachmentType::DOCUMENT);
    }

    public function incomingInvoices(): BelongsToMany
    {
        return $this->attachments()
            ->wherePivot('type', AttachmentType::INCOMING_INVOICE);
    }

    public function outgoingInvoices(): BelongsToMany
    {
        return $this->attachments()
            ->wherePivot('type', AttachmentType::OUTGOING_INVOICE);
    }
}
