<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Contracts\External;

use Illuminate\Http\UploadedFile;

/**
 * Contract for media/document operations.
 *
 * Handles file uploads and media management.
 */
interface MediaContract
{
    /**
     * Upload a document.
     */
    public function uploadDocument(UploadedFile $file, mixed $model, string $collection = 'documents'): mixed;

    /**
     * Delete a document.
     */
    public function deleteDocument(mixed $media): void;

    /**
     * Get documents for a model.
     */
    public function getDocuments(mixed $model, string $collection = 'documents'): mixed;

    /**
     * Get allowed mime types for uploads.
     */
    public function getAllowedMimetypes(): array;
}
