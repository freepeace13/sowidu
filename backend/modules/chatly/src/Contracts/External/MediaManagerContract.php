<?php

namespace Modules\Chatly\Contracts\External;

use Illuminate\Http\UploadedFile;

/**
 * Outgoing port for media/file management.
 *
 * The main application's MediaLibrary package provides the adapter.
 */
interface MediaManagerContract
{
    /**
     * Store a file and return media metadata.
     *
     * @param  UploadedFile  $file  The uploaded file
     * @param  string  $collection  Collection name (e.g., 'chat_attachments')
     * @param  mixed  $owner  The owner of the media
     * @return array Media metadata
     */
    public function store(UploadedFile $file, string $collection, mixed $owner): array;

    /**
     * Find media by ID.
     *
     * @return array|null Media metadata or null
     */
    public function find(int $mediaId): ?array;

    /**
     * Get media files for a user.
     *
     * @param  mixed  $user  The user
     * @param  array  $filters  Filters like ['type' => 'image']
     * @return array Collection of media items
     */
    public function getMediaForUser(mixed $user, array $filters = []): array;

    /**
     * Delete media by ID.
     */
    public function delete(int $mediaId): bool;

    /**
     * Get allowed MIME types for uploads.
     */
    public function getAllowedMimeTypes(): array;
}
