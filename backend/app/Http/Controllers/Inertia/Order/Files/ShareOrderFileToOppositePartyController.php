<?php

namespace App\Http\Controllers\Inertia\Order\Files;

use App\Actions\Order\File\ShareFileToOppositeParty;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Order;
use Illuminate\Http\Request;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class ShareOrderFileToOppositePartyController extends InertiaController
{
    public function __invoke(
        Request $request,
        Order $order,
        MediaFile $media,
        ShareFileToOppositeParty $sharer,
    ) {
        $sharer->share($request->user(), $order, $media, $this->getCurrentTeam());

        return back(302);
    }
}
