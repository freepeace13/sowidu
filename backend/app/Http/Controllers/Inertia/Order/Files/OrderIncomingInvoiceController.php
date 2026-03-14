<?php

namespace App\Http\Controllers\Inertia\Order\Files;

use App\Actions\Media\DeleteMedia;
use App\Actions\Order\File\IncomingInvoice\AttachIncomingInvoiceToOrder;
use App\Actions\Order\File\IncomingInvoice\DetachIncomingInvoiceFromOrder;
use App\Enums\PermissionType;
use App\Http\Controllers\Inertia\InertiaController;
use App\Http\Controllers\Traits\WithOrderService;
use App\Models\Order;
use App\Traits\InteractsWithImpersonator;
use App\Traits\WithAccountCategories;
use App\Transformers\MediaTransformer;
use App\Transformers\Order\OrderTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Packages\MediaLibrary\MediaCollections\Models\Media as MediaFile;

class OrderIncomingInvoiceController extends InertiaController
{
    use InteractsWithImpersonator, WithAccountCategories, WithOrderService;

    public function index(Request $request, Order $order)
    {
        $service = $this->createOrderService();

        abort_unless(
            $service->isOrderedByCurrentUser($order) || $service->isCurrentlyOwned($order),
            404,
            'Order not found!',
        );

        return Inertia::render('Order/Files/IncomingInvoices', [
            'order' => (new OrderTransformer($order))->resolve(),
            'orderOn' => $service->isOrderedByCurrentUser($order) ? 'outgoing' : 'incoming',
            'invoices' => $service
                ->attachmentIncomingInvoices($this->user(), $order)
                ->get()
                ->map(
                    fn ($document) => (new MediaTransformer($document))
                        ->withPolicies($this->user())
                        ->withAddressTag($document->addressTag())
                        ->withCreatedAt()
                        ->withOwner()
                        ->resolve(),
                ),
            'allowedTypes' => Arr::flatten(config('media-manager.mime_types')),

            'categories' => Inertia::lazy(fn () => transform_array(
                $this->getAccountCategories(),
                'ucfirst',
            )),
            'permissionTypes' => PermissionType::getConstants(),
        ]);
    }

    public function store(Request $request, Order $order, AttachIncomingInvoiceToOrder $attacher)
    {
        $attacher->attach($request->user(), $order, $request->all(), $this->getCurrentTeam());

        return back(303);
    }

    public function destroy(
        Request $request,
        Order $order,
        MediaFile $media,
        DetachIncomingInvoiceFromOrder $detacher,
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
        DetachIncomingInvoiceFromOrder $detacher,
    ) {
        // Detach from order first
        $detacher->detach($request->user(), $order, $media, $this->getCurrentTeam());

        return back(303);
    }
}
