<?php

namespace App\Http\Controllers\Inertia\Media;

use App\Enums\MediaCategories;
use App\Models\User;
use App\Services\MediaFileService;
use App\Support\Facades\Impersonate;
use App\Traits\InteractsWithImpersonator;
use App\Transformers\MediaTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

// use Sowidu\MediaManager\Models\MediaFolder;

class AjaxController extends BaseController
{
    use InteractsWithImpersonator;

    public function getShareSettings(Request $request, string $media)
    {
        $media = MediaFile::whereUuid($media)->first();

        $gate = Gate::forUser($this->user());

        $mediaFileService = (new MediaFileService);

        return Response::json([
            'media' => (new MediaTransformer($media))
                ->withOwner()
                ->withMembers()
                ->resolve(),

            'policies' => [
                'can_modify_permission' => $mediaFileService->canModifyPermission($this->user(), $media),
                'can_modify_members' => $mediaFileService->canModifyMembers($this->user(), $media),
            ],
        ]);
    }

    public function getSuggestions(Request $request, string $media)
    {
        $media = MediaFile::whereUuid($media)->first();

        $keyword = $request->query('search') ?? '';

        $query = User::query()->search($keyword);

        if (Impersonate::isImpersonating()) {
            $query = Impersonate::tenant()->employees()
                ->whereHas('user', function ($query) use ($keyword) {
                    $query->search($keyword);
                })->with(['user.profile.avatar']);
        }

        $suggestions = filled($keyword)
            ? $query->get()
                ->reject(function ($item) use ($media) {
                    return $media->isOwnedBy($item) || $media->wasSharedTo($item);
                })
                ->values()
            : collect();

        return Response::json([
            'suggestions' => $suggestions->map(function ($item) {
                return (new UserTransformer($item))->resolve();
            }),
        ]);
    }

    public function updateCustomField(Request $request, string $media)
    {
        $request->validate([
            'name' => ['required_with:value', 'string'],
            'value' => ['nullable'],
        ]);

        $media = MediaFile::whereUuid($media)->first();

        abort_unless((bool) $media, 404);

        $media->metaSet($request->name, $request->value);

        $media->metaSet('edited_by', $request->user()->full_name);
        $media->metaSet('edited_at', now()->toDayDateTimeString());

        return Response::json([
            'file' => (new MediaTransformer($media))
                ->withOwner()
                ->withMembers()
                ->resolve(),
        ]);
    }

    public function updateCategory(Request $request, string $media)
    {
        $validated = $request->validate([
            'category' => ['required', 'in:' . implode(',', array_values(MediaCategories::getConstants()))],
        ]);

        $media = MediaFile::whereUuid($media)->first();

        abort_unless((bool) $media, 404);

        $media
            ->metaReset()
            ->metaSet('category', $validated['category']);

        return Response::json([
            'file' => (new MediaTransformer($media))
                ->withOwner()
                ->withMembers()
                ->resolve(),
        ]);
    }

    /** @deprecated */
    // public function getFolderContent(MediaFolder $folder)
    // {
    //     $user = Impersonate::impersonator() ?? Impersonate::user();

    //     $folders = $folder->exists
    //         ? $user->getFolders()->getFolders($folder)
    //         : $user->getFolders()->getRootFolders();

    //     return Response::json([
    //         'current_folder' => [
    //             'id' => $folder->id,
    //             'key' => $folder->uuid,
    //             'name' => $folder->name ?? 'My Drive',
    //             'parent_folder_key' => $folder->folder->uuid,
    //             'folders' => $folders->map(function ($folder) {
    //                 return [
    //                     'id' => $folder->id,
    //                     'key' => $folder->uuid,
    //                     'name' => $folder->name,
    //                 ];
    //             }),
    //         ],
    //     ]);
    // }

    public function show(string $media)
    {
        $media = MediaFile::whereUuid($media)->first();

        abort_if(is_null($media), 404);

        $file = [
            'id' => $media->getKey(),
            'key' => $media->uuid,
            'is_dir' => false,
            'name' => $media->name,
        ];

        $file = array_merge($file, [
            'file_type' => $media->type,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'url' => $media->getUrl(),
            'size' => $media->getHumanReadableSizeAttribute(),
        ]);

        return Response::json(compact('file'));
    }

    public function preview(string $media)
    {
        $media = MediaFile::whereUuid($media)->first();

        return Response::json([
            'file' => [
                'id' => $media->id,
                'key' => $media->uuid,
                'name' => $media->name,
                'file_type' => $media->type,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'url' => $media->getUrl(),
                'size' => $media->getHumanReadableSizeAttribute(),
            ],
        ]);
    }

    public function getImages(Request $request)
    {
        $user = (Impersonate::impersonator() ?? Impersonate::user());

        $images = $user
            ->mediaFiles()
            ->whereIn('mime_type', ['image/jpeg', 'image/png'])
            ->get();

        return response()->json([
            'files' => $images->map(function ($media) {
                return [
                    'id' => $media->getKey(),
                    'key' => $media->uuid,
                    'file_name' => $media->file_name,
                    'url' => $media->getUrl(),
                    'conversions' => $media->getConversionUrls(),
                ];
            }),
        ], 200);
    }
}
