<?php

namespace App\Actions\Order\File\IncomingInvoice;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class DetachIncomingInvoiceFromOrder
{
    public function detach(User $user, Order $order, Media $mediaFile, ?Company $company): Order
    {
        $service = OrderService::make($user, $company);

        throw_validation_unless(
            $service->isInvolvedOnOrder($order),
            'You are not involved on this order.',
        );

        $service->detachAttachment($order, $mediaFile);

        return $order;
    }
}
