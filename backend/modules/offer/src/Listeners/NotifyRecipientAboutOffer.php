<?php

namespace Modules\Offer\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Offer\Contracts\External\AddressbookServiceContract;
use Modules\Offer\Events\OfferSent;
use Modules\Offer\Notifications\OfferSentNotification;

class NotifyRecipientAboutOffer implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'broadcasts';

    public function __construct(
        protected AddressbookServiceContract $addressbookService,
    ) {}

    public function handle(OfferSent $event): void
    {
        $offer = $event->offer;
        $recipientEmail = $offer->recipient->email ?? null;

        if (!$recipientEmail) {
            return;
        }

        $user = $this->addressbookService->findUserByEmail($recipientEmail);

        if (!$user) {
            return;
        }

        $user->notify(new OfferSentNotification($offer));
    }
}
