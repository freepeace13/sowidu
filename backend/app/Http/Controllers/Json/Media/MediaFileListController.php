<?php

namespace App\Http\Controllers\Json\Media;

use App\Http\Controllers\Json\BaseController;
use App\Models\Order;
use App\Services\MediaFileService;
use App\Transformers\MediaTransformer;
use Illuminate\Http\Request;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class MediaFileListController extends BaseController
{
    public function __invoke(Request $request, MediaFileService $service)
    {
        $user = $this->user();

        // TODO: Move this one to its model - after media package is moved to project dir
        MediaFile::resolveRelationUsing('attachToOrders', function ($mediaModel) {
            return $mediaModel->belongsToMany(
                Order::class,
                'order_attachment',
                'media_file_id',
                'order_id',
            );
        });

        return response()->json(
            $service->forUser($user, $this->getCurrentTeamId())
                ->with(['attachToOrders:id,order_number'])
                ->filters($request->only(['address', 'type', 'category', 'noAddress', 'noCategory']))
                ->latest()
                ->orderBy('id', 'asc')
                ->paginate($request->get('count', 24))
                ->through(function (MediaFile $media) use ($user, $request) {
                    $media->loadMissing(['model']);

                    $payload = $request->get('payload');

                    if ($payload === 'basic') {
                        return (new MediaTransformer($media))->resolve();
                    }

                    return (new MediaTransformer($media))
                        ->withPolicies($user)
                        ->withIsShared($user)
                        ->withAddressTag($media->addressTag())
                        ->withCreatedAt()
                        ->withAttachedToOrders($media->attachToOrders)
                        ->withOwner()
                        ->resolve();
                }),
        );
    }
}
