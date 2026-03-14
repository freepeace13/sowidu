<?php

namespace App\Listeners\Order;

use App\Events\Order\IncomingOrderCreated;
use App\Mail\OrderInvitationToSignUpMail;
use App\Models\Addressbook;
use App\Models\Company;
use App\Notifications\Order\Client\NewOutgoingOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotifyClientAboutIncomingOrder implements ShouldQueue
{
    public function handle(IncomingOrderCreated $event)
    {
        $order = $event->order;
        // Check if client is registered or USER

        $client = $order->client;

        // CLient that are not registered - send mail invitation
        if (morph_is($client, Addressbook::class)) {
            if ($client->isForeignPerson()) {
                return;
                // TODO: Uncomment this when we support sending order email notification on foreign/unregistered person
                // return Mail::to($client->email)->send(
                //     new OrderInvitationToSignUpMail($order, $client, $order->contractor)
                // );
            }
        }

        // Ignore client that are not registered
        if (!morph_is($order->client, User::class) && !morph_is($client, Company::class)) {
            return;
        }

        if (morph_is($client, Company::class)) {
            // Notify employees
            return $client->employees()
                ->get()
                ->each(fn ($employee) => Notification::send(
                    $employee,
                    new NewOutgoingOrderNotification(
                        $order,
                        $order->userAuthor,
                    ),
                ));
        }

        // Notify client about the new order
        Notification::send(
            $client,
            new NewOutgoingOrderNotification($order, $order->userAuthor),
        );
    }
}
