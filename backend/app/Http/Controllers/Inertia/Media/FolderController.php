<?php

namespace App\Http\Controllers\Inertia\Media;

use App\Transformers\MediaTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Sowidu\MediaManager\Models\MediaFolder;

/** @deprecated */
class FolderController extends BaseController
{
    public function show(MediaFolder $folder)
    {
        $repository = $this->user()->getFolders();

        $folders = $folder->exists
            ? $repository->getFolders($folder)
            : $repository->getRootFolders();

        if ($this->shouldRespondJson()) {
            $data = (new MediaTransformer($folder))
                ->withFolder()
                ->resolve();

            if ($target = request()->query('target', null)) {
                $target = $this->resolveMedia($target);

                $gate = Gate::forUser($this->user());

                $data['policies'] = [
                    'can_move_here' => $gate->allows('move-here', [$folder, $target]),
                ];

                $folders = $folders
                    ->filter(function ($folder) use ($target, $gate) {
                        return $gate->allows('move-here', [$folder, $target]);
                    })->values();
            }

            return Response::json([
                'folder' => array_merge($data, [
                    'folders' => $folders->map(function ($folder) {
                        return (new MediaTransformer($folder))->resolve();
                    }),
                ]),
            ]);
        }
    }

    public function store(Request $request, MediaFolder $folder)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3'],
        ]);

        return DB::transaction(function () use ($validated, $folder) {
            ($folder->exists ? $folder->model : $this->user())
                ->addFolder($validated['name'])
                ->setParent($folder)
                ->save();

            return back(303);
        });
    }
}
