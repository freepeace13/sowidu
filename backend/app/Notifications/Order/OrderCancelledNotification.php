<?php

namespace App\Notifications\Order;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Transformers\CompanyTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public User|Company $causer)
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

    public function toArray($notifiable)
    {
        $causer = $this->causer;

        return [
            'message' => __('order.notifications.order.cancelled', [
                'causer' => $causer?->name ?? $causer?->full_name,
            ]),
            'causer' => model_is($causer, 'company')
                ? (new CompanyTransformer($causer))
                : (new UserTransformer($causer)),
            'redirectTo' => route('orders.incoming.show', ['order' => $this->order]),
        ];
    }
}
