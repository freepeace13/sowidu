<?php

namespace App\Notifications\Order\Contractor;

use App\Models\Order;
use App\Models\User;
use App\Transformers\CompanyTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewIncomingOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Order $order, protected User $causer)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $client = $this->order->client;

        return [
            'message' => __('order.notifications.outgoing.contractor-new-incoming-order', [
                'client' => $client?->name ?? $client?->full_name,
            ]),
            'causer' => model_is($client, 'company')
                ? (new CompanyTransformer($client))
                : (new UserTransformer($client)),
            'redirectTo' => route('orders.incoming.show', ['order' => $this->order]),
        ];
    }
}
