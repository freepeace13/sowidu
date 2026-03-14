<?php

namespace App\Http\Controllers\Inertia\Media;

use App\Http\Controllers\Inertia\InertiaController;
use App\Services\MediaFileService;
use App\Transformers\MediaTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;
use Sowidu\MediaManager\Models\MediaFolder;

class TrashController extends InertiaController
{
    public function index(Request $request, MediaFileService $service)
    {
        $user = $this->user();

        return Inertia::render('Media/Trash', [
            'files' => $service->forUser($user, $this->getCurrentTeamId())
                ->latest()
                ->onlyTrashed()
                ->get()
                ->map(function (MediaFile $media) use ($user) {
                    return (new MediaTransformer($media))
                        ->withPolicies($this->user())
                        ->withAddressTag($media->addressTag())
                        ->withCreatedAt()
                        ->withOwner()
                        ->withIsShared($user)
                        ->resolve();
                }),
        ]);
    }

    public function restore(string $media)
    {
        $media = MediaFile::withTrashed()->firstWhere('uuid', $media);

        abort_unless((bool) $media, 404);

        $media->restore();

        return back(303);
    }

    public function emptyTrash()
    {
        $this->user()->mediaFiles()->onlyTrashed()->forceDelete();

        $folders = $this->user()->mediaFolders()->onlyTrashed()->get();

        foreach ($folders as $folder) {
            $this->deleteFilesRecursively($folder);
        }

        return back(303);
    }

    public function destroy(string $media)
    {
        $media = MediaFile::withTrashed()->firstWhere('uuid', $media);

        abort_unless((bool) $media, 404);

        $this->authorizeForUser($user = $this->user(), 'force-delete', $media);

        if ($media->isFolder()) {
            $this->deleteFilesRecursively($media);
        } else {
            $media->forceDelete();
        }

        return back(303);
    }

    protected function deleteFilesRecursively(MediaFolder $folder)
    {
        $folder->files->each->forceDelete();

        foreach ($folder->folders as $folder) {
            $this->deleteFilesRecursively($folder);
        }

        $folder->forceDelete();
    }
}
