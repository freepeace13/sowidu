<?php

namespace App\Services;

use App\Models\AddressRecord;
use App\Models\Company as Team;
use App\Models\Place as Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class AddressRecordService
{
    protected $query;

    public function __construct(protected User $user, protected null|int|Team $team = null)
    {
        if (is_int($team)) {
            $this->team = Team::findOrFail($team);
        }

        $this->query = $this->newQuery();
    }

    protected function setQuery(Builder $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * @return \App\Models\AddressRecord|Builder
     */
    public function newQuery()
    {
        return AddressRecord::query()
            ->whereTeamId($this->team?->id)
            ->when(is_null($this->team?->id), function ($query) {
                $query->whereUserId($this->user->id);
            });
    }

    /**
     * @return \App\Models\AddressRecord|static
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, get_class_methods(get_class()))) {
            return $this->{$method}(...$parameters);
        }

        $result = $this->query->{$method}(...$parameters);

        if ($result instanceof Builder) {
            return $this;
        }

        $this->setQuery($this->newQuery());

        return $result;
    }

    /**
     * @return \App\Models\AddressRecord|static
     */
    public static function make(User $user, null|int|Team $team = null): self
    {
        return new static($user, $team);
    }

    protected function createAddress(string $completeAddress, array $details = []): AddressRecord
    {
        return $this->query->create([
            'team_id' => $this->team?->id,
            'user_id' => $this->user->id,
            'complete_address' => $completeAddress,
            'details' => $details,
        ]);
    }

    public function tagAddressToMedia(Address $address, Media $mediaFile): AddressRecord
    {
        // Check if we already have a record on this address
        if (!$record = $this->findFromCompleteAddress($address->complete_address)) {
            $record = $this->createAddress(
                $address->complete_address,
                $address->only([
                    'house_number',
                    'street',
                    'zipcode',
                    'state',
                    'city',
                    'country',
                    'country_name',
                ]),
            );
        }

        return tap($record)->tagToMedia($mediaFile);
    }

    public function findFromCompleteAddress(string $completeAddress): ?AddressRecord
    {
        return $this->query
            ->where('complete_address', $completeAddress)
            ->first();
    }
}
