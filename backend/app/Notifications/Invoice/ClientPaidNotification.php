<?php

namespace App\Notifications\Invoice;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Order;
use App\Modules\Invoice\InvoiceService;
use App\Transformers\CompanyTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientPaidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Company $causer;

    public $amount;

    public function __construct(public Invoice $invoice, public Order $order)
    {
        $this->causer = $invoice->company;
        $this->amount = InvoiceService::run($invoice)->totalAmount();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(
                trans('invoices.mail.paid.subject', [
                    'invoice' => $this->invoice->internal_id,
                ]),
            )
            ->markdown('emails.paid-invoice-to-client', [
                'totalAmount' => $this->amount,
                'invoice' => $this->invoice,
                'order' => $this->order,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $causer = $this->causer;

        return [
            'message' => __('invoices.notifications.client.paid', [
                'invoice' => $this->invoice->internal_id,
            ]),
            'causer' => model_is($causer, 'company')
                ? (new CompanyTransformer($causer))
                : (new UserTransformer($causer)),
            'redirectTo' => route('orders.show.invoices.show', [
                'order' => $this->invoice->order_id,
                'invoice' => $this->invoice->id,
            ]),
        ];
    }
}
