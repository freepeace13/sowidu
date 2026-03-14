<?php

namespace App\Actions\Media;

use App\Actions\AddressRecord\CreatesAddressRecord;
use App\Actions\Rules\WithAddressRules;
use App\Models\Place;
use App\Models\User;
use App\Services\AddressRecordService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class CreateMediaAddressTag
{
    use WithAddressRules;

    public function tag(User $user, Media $media, array $inputs, ?int $teamId = null)
    {
        // TODO add gate policy
        $validated = Validator::make($inputs, $this->addressRules(), $this->addressMessages())->validate();

        if ($addressId = Arr::get($inputs, 'address.id')) {
            $address = Place::findOrFail($addressId);
        } else {
            // Create address first
            $address = (new CreatesAddressRecord)->create($user, Arr::only($validated, 'address'));
        }

        AddressRecordService::make($user, $teamId)->tagAddressToMedia($address, $media);
    }
}
