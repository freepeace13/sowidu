<?php

namespace Modules\Offer\Notifications;

use Illuminate\Notifications\Notification;
use Modules\Offer\Models\Offer;

class OfferSentNotification extends Notification
{
    public function __construct(public Offer $offer) {}

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
            'offer.notifications.new_offer',
            [
                'sender' => $this->offer->company->name ?? '',
            ],
        );
    }

    protected function redirectTo(): ?string
    {
        return route('my-offers.show', $this->offer);
    }

    protected function causer(): array
    {
        $company = $this->offer->company;

        return [
            'name' => $company->name ?? '',
            'avatar' => get_company_avatar_url($company),
        ];
    }
}
