<?php

namespace App\Actions;

use App\Contracts\Auth\AuthorizableGroup;
use App\Contracts\Contactable;
use App\Events\Contact\RelationshipUpdate;
use App\Repositories\ContactRepository;

class CreateContact
{
    /**
     * @var \App\Contracts\Auth\AuthorizableGroup
     */
    protected $group;

    /**
     * @return void
     */
    public function __construct(AuthorizableGroup $group)
    {
        $this->group = $group;
    }

    /**
     * @param  int|null  $invitationId
     * @return \App\Models\Contact
     */
    public function __invoke(Contactable $model, $invitationId = null)
    {
        $repository = app()->make(ContactRepository::class);

        if (!$contact = $repository->findFrom($this->group, $model)) {
            $contact = $this->group->contacts()->create([
                'contactable_id' => $model->getKey(),
                'contactable_type' => $model->getMorphClass(),
                'preference_data' => [],
                'confirmed' => true,
                'invitation_id' => $invitationId,
            ]);

            RelationshipUpdate::broadcast($contact->ownerable, $contact->contactable);
        }

        return $contact;
    }
}
