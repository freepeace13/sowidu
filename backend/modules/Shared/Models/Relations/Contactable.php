<?php

namespace Modules\Shared\Models\Relations;

use App\Models\Contact;
use App\Models\ContactNumber;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

trait Contactable
{
    /**
     * Determine if the contactable matched the type
     */
    public function isContactTypeOf(string $type): bool
    {
        return Str::is($this->getContactType(), $type);
    }

    /**
     * Get the contactable contact instance from authenticatable who added you
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function contactables(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    /**
     * Get the user's contact numbers
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function contactNumbers(): MorphMany
    {
        return $this->morphMany(ContactNumber::class, 'ownerable');
    }
}
