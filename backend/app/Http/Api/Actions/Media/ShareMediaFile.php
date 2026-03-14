<?php

namespace App\Http\Api\Actions\Media;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\Media\MediaSharedNotification;
use App\Rules\UrnRule;
use App\Services\FileSharingService;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\MediaCollections\Models\MediaShare;
use Packages\RestApi\RestApiAction;
use Packages\Urn\UrnManager;

class ShareMediaFile extends RestApiAction
{
    public function __construct(
        private FileSharingService $fileSharingService,
    ) {}

    public function getValidationRules(): array
    {
        return [
            'media' => ['required', 'exists:media_files,uuid'],
            'user' => ['required', new UrnRule([User::class, Employee::class])],
            'scopes' => ['required', 'in:' . implode(',', [MediaShare::READONLY, MediaShare::READWRITE])],
        ];
    }

    public function share(Media $media, string $user, $scopes = MediaShare::READONLY, $errorBag = null)
    {
        $validated = $this->validate([
            'media' => $media->uuid,
            'user' => $user,
            'scopes' => $scopes ?? MediaShare::READONLY,
        ], $errorBag);

        $user = UrnManager::resolve($validated['user']);

        $this->fileSharingService->setMedia($media);

        if ($this->fileSharingService->isBelongsTo($user)) {
            $this->throwValidationError([
                'user' => 'File owner permissions are immutable.',
            ], $errorBag);
        }

        $this->fileSharingService->shareTo($user, $validated['scopes']);

        $user->notify(new MediaSharedNotification($this->fileSharingService->getMedia()));

        return $user;
    }
}
