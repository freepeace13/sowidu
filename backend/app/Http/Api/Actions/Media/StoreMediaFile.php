<?php

namespace App\Http\Api\Actions\Media;

use App\Events\Media\NewMediaAdded;
use App\Models\Company as Team;
use App\Models\User;
use Carbon\Carbon;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\RestApi\RestApiAction;

class StoreMediaFile extends RestApiAction
{
    protected $fileName;

    protected $rules = [
        'file' => [
            'file',
            'mimetypes:video/mp4,video/quicktime,application/pdf,image/jpeg,image/png,image/jpg',
        ],
        'last_modified' => ['nullable', 'date'],
    ];

    public function store(User $user, array $data, $teamId = null, $errorBag = null)
    {
        $validated = $this->validate($data, $errorBag);

        $file = $validated['file'];
        $lastModified = $validated['last_modified'];

        $team = Team::find($teamId);

        if (!$user->can('store', [Media::class, $team])) {
            abort(403);
        }

        $fileAdder = ($team ? $user->teamMembership($team) : $user)
            ->addMedia($file)
            ->withResponsiveImages()
            ->withCustomProperties([
                'lastModified' => $lastModified
                    ? Carbon::createFromTimestamp($lastModified)->toDateTimeString()
                    : null,
            ]);

        if ($this->fileName) {
            $fileAdder
                ->setFileName($this->fileName)
                ->setName(pathinfo($this->fileName, PATHINFO_FILENAME));
        }

        $media = $fileAdder->save();

        NewMediaAdded::dispatch($media);

        return $media;
    }

    public function withFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }
}
