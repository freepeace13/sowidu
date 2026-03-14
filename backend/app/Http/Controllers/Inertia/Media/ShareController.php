<?php

namespace App\Http\Controllers\Inertia\Media;

use App\Actions\Media\Share\CreateMediaSharing;
use App\Actions\Media\Share\RemoveMediaSharing;
use App\Actions\Media\Share\UpdateMediaSharing;
use App\Traits\InteractsWithMedia;
use Illuminate\Http\Request;

class ShareController extends BaseController
{
    use InteractsWithMedia;

    public function store(Request $request, string $media)
    {
        (new CreateMediaSharing)->create($request->user(), $media, $request->all());

        return back(303);
    }

    public function update(Request $request, string $media)
    {
        (new UpdateMediaSharing)->update($request->user(), $media, $request->all());

        return back(303);
    }

    public function destroy(Request $request, string $media)
    {
        (new RemoveMediaSharing)->remove($request->user(), $media, $request->all());

        return back(303);
    }
}
