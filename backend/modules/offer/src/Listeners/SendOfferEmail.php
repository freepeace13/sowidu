<?php

namespace Modules\Offer\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Offer\Actions\GenerateOfferPdf;
use Modules\Offer\Events\OfferSent;
use Modules\Offer\Mail\OfferNotification;
use Modules\Offer\OfferService;

class SendOfferEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'emails';

    public function handle(OfferSent $event)
    {
        $offer = $event->offer;
        $recipient_email = $offer->recipient->email ?? null;

        if (!$recipient_email) {
            return;
        }

        $pdf = GenerateOfferPdf::run($offer);
        $path = OfferService::make($offer)->storagePath();
        $pdf->save($path);

        Mail::to($recipient_email)
            ->send(new OfferNotification($offer, $path));
    }
}
