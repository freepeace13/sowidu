<?php

namespace App\Models\Concerns;

use App\Models\Place;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

trait HasDeliveryPlace
{
    use HasPlaces;

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'delivery_address_record_id');
    }

    public function addOrUpdateDeliveryAddress(string $label, array $inputs)
    {
        if ($placeId = Arr::get($inputs, 'id')) {
            $this->update(['delivery_address_record_id' => $placeId]);
        }

        $deliveryAddress = $this->deliveryAddress;

        if (!$deliveryAddress) {
            return $this->addPlace($label, $inputs);
        }

        return $this->updatePlace($deliveryAddress, $inputs);
    }

    public function currentPlace()
    {
        if ($this->ownedPlaces->isNotEmpty() && !$this->current_place_id) {
            $this->switchPlace($this->ownedPlaces->first());
        }

        return $this->belongsTo(Place::class, 'delivery_address_record_id');
    }

    public function switchPlace($place)
    {
        if (!$this->ownsPlace($place)) {
            return false;
        }

        if (!$this->isCurrentPlace($place)) {
            $this->forceFill([
                'delivery_address_record_id' => $place->id,
            ])->save();

            $this->setRelation('deliveryAddress', $place);
        }

        return true;
    }
}
