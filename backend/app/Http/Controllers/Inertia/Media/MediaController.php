<?php

namespace App\Http\Controllers\Inertia\Media;

use App\Actions\Media\DeleteMedia;
use App\Enums\MediaCategories;
use App\Services\MediaFileService;
use App\Traits\InteractsWithImpersonator;
use App\Traits\WithAccountCategories;
use App\Transformers\MediaTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Packages\MediaLibrary\HasMedia;
use Packages\MediaLibrary\MediaCollections\Filesystem;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Sowidu\MediaManager\Models\MediaFolder;

class MediaController extends BaseController
{
    use InteractsWithImpersonator, WithAccountCategories;

    public function index(Request $request)
    {
        return Inertia::render('Media/Index', [
            'allowedTypes' => Arr::flatten(config('media-library.mime_types')),
            'categories' => transform_array($this->getAccountCategories(), 'ucfirst'),
            'totalCount' => fn () => MediaFileService::make(
                $this->user(),
                $this->getCurrentTeamId(),
            )->count(),
        ]);
    }

    public function updateCategory(Request $request, string $media)
    {
        $validated = $request->validate([
            'category' => ['required', 'in:' . implode(',', array_values(MediaCategories::getConstants()))],
        ]);

        $media = Media::whereUuid($media)->first();

        abort_unless((bool) $media, 404);

        $media->metaSet('category', $validated['category']);

        if ($this->shouldRespondJson()) {
            return Response::json([
                'file' => (new MediaTransformer($media))
                    ->withOwner()
                    ->withMembers()
                    ->resolve(),
            ]);
        }
    }

    public function show(string $media)
    {
        $media = Media::whereUuid($media)->first();
        $user = $this->user();

        abort_unless((bool) $media, 404);

        if ($this->shouldRespondJson()) {
            return response()->json([
                'file' => (new MediaTransformer($media))
                    ->withPolicies($user)
                    ->withIsShared($user)
                    ->withAddressTag($media->addressTag())
                    ->withCreatedAt()
                    ->withOwner()
                    ->withMembers()
                    ->resolve(),
            ]);
        }
    }

    public function move(Request $request, string $media)
    {
        $media = Media::whereUuid($media)->first();

        abort_if(is_null($media), 404);

        $validated = $request->validate([
            'to' => ['nullable', 'exists:media_folders,uuid'],
        ]);

        $media->folder_id = null;

        if ($newParent = $validated['to']) {
            $media->folder()->associate(
                MediaFolder::findByUuid($newParent),
            );
        }

        $media->save();

        return back(303);
    }

    public function destroy(string $media)
    {
        $media = Media::whereUuid($media)->first();

        abort_if(is_null($media), 404);

        (new DeleteMedia)->delete($this->user(), $media);

        return back(303);
    }

    public function rename(Request $request, string $media)
    {
        $media = Media::whereUuid($media)->first();

        abort_if(is_null($media), 404);

        $validated = $request->validate([
            'name' => ['required', 'min:3'],
        ]);

        $media->name = $validated['name'];

        $mediaName = pathinfo($validated['name'], PATHINFO_FILENAME);
        $extension = pathinfo($validated['name'], PATHINFO_EXTENSION);

        if (blank($extension)) {
            $extension = $media->extension;
        }

        $fileName = "{$mediaName}.{$extension}";

        app(Filesystem::class)->syncFileNames($media->forceFill([
            'file_name' => $fileName,
            'name' => $mediaName,
        ]));

        $media->save();

        return back(303);
    }

    /**
     * @param  HasMedia|\App\Models\User|\App\Models\Employee  $user
     * @param  array  $filters
     * @return void
     */
    protected function getUserFiles($user, MediaFolder $folder, $filters = [])
    {
        // NOTE: Do not remove!!
        // $folders = $folder->exists
        //     ? $user->getFolders()->getFolders($folder, $filters)
        //     : $user->getFolders()->getRootFolders($filters);

        $folders = collect();
        $files = $user->getMedia()->getRootFiles($filters);

        return [$folders, $files];
    }
}
