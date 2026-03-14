<?php

namespace App\Notifications\Order\Client;

use App\Models\Order;
use App\Models\User;
use App\Transformers\CompanyTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewOutgoingOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Order $order, protected User $causer)
    {
        //
    }

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
        $contractor = $this->order->contractor;

        return [
            'message' => __('order.notifications.incoming.client-new-outgoing-order', [
                'contractor' => $contractor->name,
            ]),
            'causer' => (new CompanyTransformer($contractor)),
            'redirectTo' => route('orders.outgoing.show', ['order' => $this->order]),
        ];
    }
}
