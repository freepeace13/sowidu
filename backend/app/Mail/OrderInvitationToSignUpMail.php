<?php

namespace App\Mail;

use App\Models\Addressbook;
use App\Models\Company;
use App\Models\Order;
use Crypt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderInvitationToSignUpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public Addressbook $client, public Company $company)
    {
        //
    }

    /**
     * Build the message.
     *
     * @return self
     */
    public function build()
    {
        return $this
            ->markdown('mail.order-invitation-to-sign-up-mail')
            ->with([
                'addressbookCrypt' => Crypt::encrypt($this->client->id),
            ]);
    }
}
