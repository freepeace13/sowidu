<?php

namespace App\Actions\Order\File;

use App\Events\Order\OrderFileShareToOtherParty;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\MediaFileService;
use App\Services\Order\OrderService;
use App\Traits\InteractsWithImpersonator;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class ShareFileToOppositeParty
{
    use InteractsWithImpersonator;

    public function share(
        User $user,
        Order $order,
        Media $media,
        ?Company $company,
    ): Order {
        $media = MediaFileService::make(
            $this->user(),
        )->findByUuidOrFail($media->uuid, ['id']);

        $service = OrderService::make($user, $company);

        // Identify opposite party if client or contractor
        $oppositeParty = $service->getOppositeParty($order, $user, $company);

        event(
            new OrderFileShareToOtherParty(
                $order,
                $media,
                $this->user(),
                $user,
                $company,
                $oppositeParty,
            ),
        );

        return $order;
    }
}
