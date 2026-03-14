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

class OrderPrimaryDetailsUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public User|Company $causer, public array $changes)
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
        $changes = implode(', ', $this->changes);
        $causer = $this->causer;

        return [
            'message' => __('order.notifications.order.updated', [
                'causer' => $causer?->name ?? $causer?->full_name,
                'changes' => $changes,
            ]),
            'causer' => model_is($causer, 'company')
                ? (new CompanyTransformer($causer))
                : (new UserTransformer($causer)),
            'redirectTo' => route('orders.incoming.show', ['order' => $this->order]),
        ];
    }
}
