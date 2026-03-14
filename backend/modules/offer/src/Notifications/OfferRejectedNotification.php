<?php

namespace Modules\Offer\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Modules\Offer\Models\Offer;

class OfferRejectedNotification extends Notification
{
    protected string $notificationRoute;

    public function __construct(public Offer $offer, public Model $rejectedBy)
    {
        $this->notificationRoute = route('offers.show', ['offer' => $this->offer]);

        $employeeClass = config('offer.models.employee', \App\Models\Employee::class);
        if ($this->rejectedBy instanceof $employeeClass) {
            $this->notificationRoute = route('my-offers.show', ['offer' => $this->offer]);
        }
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message(),
            'redirect_to' => $this->redirectTo(),
            'causer' => $this->causer(),
        ];
    }

    protected function message(): string
    {
        return __(
            'offer.notifications.offer_rejected',
            [
                'causer' => $this->rejectedBy->full_name ?? '',
            ],
        );
    }

    protected function redirectTo(): ?string
    {
        return $this->notificationRoute;
    }

    protected function causer(): array
    {
        $employeeClass = config('offer.models.employee', \App\Models\Employee::class);
        if ($this->rejectedBy instanceof $employeeClass) {
            $causer = $this->offer->company()->first();

            return [
                'name' => $causer->name ?? '',
                'avatar' => get_company_avatar_url($causer),
            ];
        }

        $causer = $this->rejectedBy;

        return [
            'name' => $causer->full_name ?? '',
            'avatar' => get_user_avatar_url($causer),
        ];
    }
}
