<?php

namespace App\Http\Controllers\Inertia\Order\Files;

use App\Actions\Media\DeleteMedia;
use App\Actions\Order\File\Media\AttachMediaToOrder;
use App\Actions\Order\File\Media\DetachMediaFromOrder;
use App\Enums\PermissionType;
use App\Http\Controllers\Inertia\InertiaController;
use App\Http\Controllers\Traits\WithOrderService;
use App\Models\Order;
use App\Services\MediaFileService;
use App\Traits\InteractsWithImpersonator;
use App\Traits\WithAccountCategories;
use App\Transformers\MediaTransformer;
use App\Transformers\Order\OrderTransformer;
use App\Transformers\PaginatorTransformer;
use Illuminate\Http\Request;
use IlluminateAgnostic\Str\Support\Arr;
use Inertia\Inertia;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class OrderMediaController extends InertiaController
{
    use InteractsWithImpersonator, WithAccountCategories, WithOrderService;

    public function index(Request $request, Order $order)
    {
        $service = $this->createOrderService();
        abort_unless(
            $service->isOrderedByCurrentUser($order) || $service->isCurrentlyOwned($order),
            404,
            trans('order.errors.order-not-found'),
        );

        $filters = $request->only([
            'address',
            'type',
            'category',
            'noAddress',
            'noCategory',
        ]);

        $mediaCollection = $service
            ->attachmentMedias(
                $this->user(),
                $order,
                $filters,
            )
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        return Inertia::render('Order/Files/OrderMedias', [
            'order' => (new OrderTransformer($order))->withDeliveryAddress()
                ->resolve(),
            'orderOn' => $service->isOrderedByCurrentUser($order) ? 'outgoing' : 'incoming',
            'medias' => $mediaCollection->map(
                fn ($media) => (new MediaTransformer($media))
                    ->withPolicies($this->user())
                    ->withAddressTag($media->addressTag())
                    ->withCreatedAt()
                    ->withOwner()
                    ->resolve(),
            ),
            'paginator' => (new PaginatorTransformer($mediaCollection))->resolve(),
            'allowedTypes' => Arr::flatten(MediaFileService::allowedMimetypes()),
            'categories' => Inertia::lazy(
                fn () => transform_array(
                    $this->getAccountCategories(),
                    'ucfirst',
                ),
            ),
            'permissionTypes' => PermissionType::getConstants(),
            'filters' => $filters,
        ]);
    }

    public function store(Request $request, Order $order, AttachMediaToOrder $attacher)
    {
        $attacher->attach($request->user(), $order, $request->all(), $this->getCurrentTeam());

        flash_success(trans('order.notifications.order-media.attached'));

        return back(303);
    }

    public function destroy(
        Request $request,

        Order $order,
        MediaFile $media,
        DetachMediaFromOrder $detacher,
    ) {
        $detacher->detach($request->user(), $order, $media, $this->getCurrentTeam());

        // Delete media
        (new DeleteMedia)->delete($this->user(), $media);

        return back(303);
    }

    public function detach(
        Request $request,
        Order $order,
        MediaFile $media,
        DetachMediaFromOrder $detacher,
    ) {
        // Detach from order first
        $detacher->detach($request->user(), $order, $media, $this->getCurrentTeam());

        flash_success(trans('order.notifications.order-media.detached'));

        return back(303);
    }
}
