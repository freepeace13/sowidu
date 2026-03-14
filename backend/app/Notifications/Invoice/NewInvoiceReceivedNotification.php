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

class NewInvoiceReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Company $causer;

    public function __construct(public Invoice $invoice, public Order $order)
    {
        $this->causer = $invoice->company;
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
                trans('invoices.mail.send-to-client.subject', [
                    'company' => $this->invoice->company->name,
                    'invoice' => $this->invoice->internal_id,
                ]),
            )
            ->markdown('emails.invoice-to-client', [
                'totalAmount' => InvoiceService::run($this->invoice)
                    ->totalAmount(),
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
        $causerName = $this->causer?->name ?? $this->causer?->full_name;

        return [
            'message' => __('invoices.notifications.client.sent', [
                'causer' => $causerName,
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
