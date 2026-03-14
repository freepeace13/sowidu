<?php

namespace App\Http\Controllers\Inertia\Media;

use App\Transformers\MediaTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;
use Sowidu\MediaManager\Models\MediaFolder;

class StarredController extends MediaController
{
    public function index(Request $request)
    {
        $rootFolders = ($user = $this->user())
            ->getFolders()
            ->getRootFolders();

        $folders = $this->buildQuery(MediaFolder::query(), $user)
            ->orderBy('name')
            ->get();

        $files = $this->buildQuery(MediaFile::query(), $user)
            ->orderBy('name')
            ->get();

        return Inertia::render('Media/Starred', [
            'root_folders' => $rootFolders
                ->map(function (MediaFolder $media) use ($user) {
                    return (new MediaTransformer($media))
                        ->withMembers()
                        ->withPolicies($user)
                        ->resolve();
                }),
            'folders' => $folders
                ->map(function (MediaFolder $media) use ($user) {
                    return (new MediaTransformer($media))
                        ->withMembers()
                        ->withStarred($user)
                        ->withPolicies($user)
                        ->resolve();
                }),
            'files' => $files
                ->map(function (MediaFile $media) use ($user) {
                    return (new MediaTransformer($media))
                        ->withStarred($user)
                        ->withPolicies($user)
                        ->resolve();
                }),
        ]);
    }

    private function buildQuery($query, $user)
    {
        return $query->where(function ($query) use ($user) {
            $query->where(function ($query) use ($user) {
                $query
                    ->where('model_id', $user->getKey())
                    ->where('model_type', $user->getMorphClass());
            })
                ->orWhereHas('shares', function ($query) use ($user) {
                    $query->whereReadableFor($user);
                });
        })
            ->whereHas('starred', function ($query) use ($user) {
                $query
                    ->where('account_id', $user->getKey())
                    ->where('account_type', $user->getMorphClass());
            });
    }

    public function destroy(string $media)
    {
        $media = $this->resolveMedia($media);

        $media->removeFromStarred($this->user());

        return back(303);
    }

    public function store(Request $request)
    {
        $media = $this->resolveMedia($request->media);

        $media->addToStarred($this->user());

        return back(303);
    }
}
