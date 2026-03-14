<?php

namespace App\Services\Chat;

use App\Services\MediaFileService;
use App\Transformers\MediaTransformer;
use Illuminate\Http\UploadedFile;
use Modules\Chatly\Contracts\External\MediaManagerContract;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

/**
 * Adapter for media/file management.
 *
 * Wraps the application's MediaLibrary package to provide the interface
 * required by the Chatly module.
 */
class MediaManagerAdapter implements MediaManagerContract
{
    /**
     * Store a file and return media metadata.
     *
     * @param  UploadedFile  $file  The uploaded file
     * @param  string  $collection  Collection name (e.g., 'chat_attachments')
     * @param  mixed  $owner  The owner of the media
     * @return array Media metadata
     */
    public function store(UploadedFile $file, string $collection, mixed $owner): array
    {
        $media = $owner->addMedia($file)
            ->toMediaCollection($collection);

        return $this->transformMedia($media);
    }

    /**
     * Find media by ID.
     *
     * @return array|null Media metadata or null
     */
    public function find(int $mediaId): ?array
    {
        $media = MediaFile::find($mediaId);

        if (!$media) {
            return null;
        }

        return $this->transformMedia($media);
    }

    /**
     * Get media files for a user.
     *
     * @param  mixed  $user  The user
     * @param  array  $filters  Filters like ['type' => 'image']
     * @return array Collection of media items
     */
    public function getMediaForUser(mixed $user, array $filters = []): array
    {
        $query = $user->getMedia();

        // Apply type filter if provided
        if (isset($filters['type'])) {
            $query = $query->getRootFiles(['type' => $filters['type']]);
        } else {
            $query = $query->getRootFiles();
        }

        return $query->map(function (MediaFile $media) use ($user) {
            return (new MediaTransformer($media))
                ->withStarred($user)
                ->withPolicies($user)
                ->resolve();
        })->toArray();
    }

    /**
     * Delete media by ID.
     */
    public function delete(int $mediaId): bool
    {
        $media = MediaFile::find($mediaId);

        if (!$media) {
            return false;
        }

        return $media->delete();
    }

    /**
     * Get allowed MIME types for uploads.
     */
    public function getAllowedMimeTypes(): array
    {
        return MediaFileService::allowedMimetypes();
    }

    /**
     * Transform a Media model to array format.
     */
    protected function transformMedia(MediaFile $media): array
    {
        return (new MediaTransformer($media))->resolve();
    }
}
