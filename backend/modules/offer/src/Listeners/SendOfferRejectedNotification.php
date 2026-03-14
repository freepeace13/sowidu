<?php

namespace Modules\Offer\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Offer\Contracts\External\AddressbookServiceContract;
use Modules\Offer\Events\OfferRejected;
use Modules\Offer\Notifications\OfferRejectedNotification;

class SendOfferRejectedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'broadcasts';

    public function __construct(
        protected AddressbookServiceContract $addressbookService,
    ) {}

    public function handle(OfferRejected $event): void
    {
        $offer = $event->offer;
        $causer = $event->causer;
        $company = $event->company;

        // The causer is the company
        if (filled($company)) {
            // Notify the recipient of the offer
            $recipientEmail = $offer->recipient->email ?? null;
            $user = $recipientEmail ? $this->addressbookService->findUserByEmail($recipientEmail) : null;

            if (!$user) {
                return;
            }

            $user->notify(new OfferRejectedNotification($offer, $causer));

            return;
        }

        // Notify the company employees
        $company?->employees()
            ->get()
            ->each(
                fn (Model $employee) => $employee
                    ->notify(new OfferRejectedNotification($offer, $causer)),
            );
    }
}
