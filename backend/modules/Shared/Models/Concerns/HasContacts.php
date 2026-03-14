<?php

namespace Modules\Shared\Models\Concerns;

use App\Contracts\Contactable as Recipient;
use App\Models\ContactRequest;
use App\Models\Relations\OwnsContacts;

trait HasContacts
{
    use OwnsContacts;

    public function isContactExists(Recipient $recipient)
    {
        return (bool) $this->findContactForRecipient($recipient);
    }

    public function hasValidContactRequest(Recipient $recipient)
    {
        return (bool) $this->findValidContactRequestForRecipient($recipient);
    }

    public function findValidContactRequestForRecipient(Recipient $recipient)
    {
        return $this->sentContactRequests()
            ->pending()
            ->where('user_id', $recipient->id)
            ->first();
    }

    public function findContactForRecipient(Recipient $recipient)
    {
        return $this->contacts()
            ->where(function ($query) use ($recipient) {
                $query->where([
                    'contactable_id' => $recipient->getKey(),
                    'contactable_type' => $recipient->getMorphClass(),
                ]);
            })
            ->first();
    }

    public function sentContactRequests()
    {
        return $this->morphMany(ContactRequest::class, 'ownerable');
    }
}
