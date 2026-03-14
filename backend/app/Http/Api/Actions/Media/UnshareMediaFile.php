<?php

namespace App\Http\Api\Actions\Media;

use App\Models\Employee;
use App\Models\User;
use App\Rules\UrnRule;
use App\Services\FileSharingService;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\RestApi\RestApiAction;
use Packages\Urn\UrnManager;

class UnshareMediaFile extends RestApiAction
{
    public function __construct(
        private FileSharingService $fileSharingService,
    ) {}

    public function unshare(Media $media, string $user, $errorBag = null)
    {
        $validated = $this->validate([
            'media' => $media->uuid,
            'user' => $user,
        ], $errorBag);

        $user = UrnManager::resolve($validated['user']);

        $this->fileSharingService->setMedia($media);

        if ($this->fileSharingService->isBelongsTo($user)) {
            $this->throwValidationError([
                'user' => 'File owner permissions are immutable.',
            ], $errorBag);
        }

        $this->fileSharingService->unshareFrom($user);
    }

    public function getValidationRules(): array
    {
        return [
            'media' => ['required', 'exists:media_files,uuid'],
            'user' => ['required', new UrnRule([User::class, Employee::class])],
        ];
    }
}
