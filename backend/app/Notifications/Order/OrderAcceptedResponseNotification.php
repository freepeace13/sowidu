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
use Illuminate\Support\Str;

class OrderAcceptedResponseNotification extends Notification implements ShouldQueue
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

        $transKey = Str::of($this->order->status->name)
            ->lower()
            ->replace('_', '-')
            ->__toString();

        return [
            'message' => __("order.notifications.order.$transKey", [
                'causer' => $causerName,
            ]),
            'causer' => model_is($causer, 'company')
                ? (new CompanyTransformer($causer))
                : (new UserTransformer($causer)),
            'redirectTo' => route('orders.incoming.show', ['order' => $this->order]),
        ];
    }
}
