<?php

namespace App\Repositories;

use App\Contracts\Auth\AuthorizableGroup;
use App\Contracts\Contactable;
use App\Models\Contact;
use App\Models\ContactRequest;

class ContactRepository
{
    /**
     * @var array
     */
    public static $mapResources = [
        \App\Models\Employee::class => \App\Http\Resources\EmployeeResource::class,
        \App\Models\Company::class => \App\Http\Resources\CompanyResource::class,
        \App\Models\User::class => \App\Http\Resources\UserResource::class,
    ];

    /**
     * Get the resource class from the given model instance.
     *
     * @return void
     */
    public static function getContactableResource(Contactable $contactable)
    {
        if (!array_key_exists(get_class($contactable), static::$mapResources)) {
            return null;
        }

        return static::$mapResources[get_class($contactable)];
    }

    /**
     * Resolve the given contactable resource
     *
     * @return mixed
     */
    public static function resolveContactableResource(Contactable $contactable)
    {
        $resourceClass = static::getContactableResource($contactable);

        if (!is_null($resourceClass)) {
            $instance = new $resourceClass($contactable);

            return $instance->includeAttributes('preference');
        }

        return new stdClass;
    }

    /**
     * Resolve resource instance of the given contact.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public static function resolveResourceFromInstance(Contact $contact)
    {
        return static::resolveContactableResource($contact->contactable);
    }

    /**
     * Determine if given contactable is already one of contacts by the group.
     *
     * @param  App\Contracts\Auth\AuthorizableGroup  $group
     * @param  App\Contracts\Contactable  $contactable
     * @return bool
     */
    public function checkHas(AuthorizableGroup $group, Contactable $contactable)
    {
        return $this->findFrom($group, $contactable) !== null;
    }

    /**
     * Determine if given contactable is already one of confirmed contacts by the group.
     *
     * @param  App\Contracts\Auth\AuthorizableGroup  $group
     * @param  App\Contracts\Contactable  $contactable
     * @return bool
     */
    public function checkHasConfirmed(AuthorizableGroup $group, Contactable $contactable)
    {
        $contact = $this->findFrom($group, $contactable);

        return !is_null($contact) && $contact->confirmed;
    }

    /**
     * Determine if given contactable is already one of confirmed contacts by the group.
     *
     * @param  App\Contracts\Auth\AuthorizableGroup  $group
     * @param  App\Contracts\Contactable  $user
     * @return bool
     */
    public function findRequest(AuthorizableGroup $group, Contactable $user)
    {
        return $this->sentRequests($group)
            ->where('user_id', $user->id)
            ->first();
    }

    public function sentRequests(AuthorizableGroup $group)
    {
        return ContactRequest::where([
            'ownerable_id' => $group->getKey(),
            'ownerable_type' => $group->getMorphClass(),
        ])
            ->get();
    }

    /**
     * Find the contact instance by contactable.
     *
     * @param  App\Contracts\Auth\AuthorizableGroup  $group
     * @param  App\Contracts\Contactable  $contactable
     * @return App\Models\Contact|null
     */
    public function findFrom(AuthorizableGroup $group, Contactable $contactable)
    {
        return $group->contacts()->which($contactable)->first();
    }
}
