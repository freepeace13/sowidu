<?php

namespace App\Models\Relations;

use App\Actions\CreateContact;
use App\Contracts\Contactable;
use App\Models\Contact;

trait OwnsContacts
{
    /**
     * @param  int|null  $invitationId
     * @return \App\Models\Contact
     */
    public function createContact(Contactable $model, $invitationId = null)
    {
        return (new CreateContact($this))($model, $invitationId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'ownerable');
    }
}
