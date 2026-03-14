<?php

namespace Modules\Offer\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Offer\Contracts\External\AddressbookServiceContract;
use Modules\Offer\Events\OfferAccepted;
use Modules\Offer\Notifications\OfferAcceptedNotification;

class SendOfferAcceptedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'broadcasts';

    public function __construct(
        protected AddressbookServiceContract $addressbookService,
    ) {}

    public function handle(OfferAccepted $event): void
    {
        $offer = $event->offer;
        $causer = $event->causer;
        $company = $event->company;

        // The causer is the company
        $recipientEmail = $offer->recipient->email ?? null;
        if (filled($company) && filled($recipientEmail)) {
            // Notify the recipient of the offer
            $user = $this->addressbookService->findUserByEmail($recipientEmail);

            if (!$user) {
                return;
            }

            $user->notify(new OfferAcceptedNotification($offer, $causer));

            return;
        }

        // Notify the company employees
        $company->employees()
            ->get()
            ->each(
                fn (Model $employee) => $employee
                    ->notify(new OfferAcceptedNotification($offer, $causer)),
            );
    }
}
