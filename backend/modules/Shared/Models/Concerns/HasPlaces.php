<?php

namespace Modules\Shared\Models\Concerns;

use App\Models\Place;
use Exception;
use Illuminate\Support\Arr;

trait HasPlaces
{
    public function isCurrentPlace($place)
    {
        return $place->id === $this->current_place_id;
    }

    public function removePlace($place)
    {
        if (!$this->ownsPlace($place)) {
            return false;
        }

        if ($this->ownedPlaces->count() === 1) {
            throw new Exception('Cannot remove single place.');
        }

        $place->delete();

        if ($this->relationLoaded('ownedPlaces')) {
            $this->load('ownedPlaces');
        }

        $this->switchPlace($this->ownedPlaces->first());

        return true;
    }

    public function updatePlace($place, array $inputs)
    {
        if (!$this->ownsPlace($place)) {
            return false;
        }

        $place->update(Arr::only($inputs, [
            'type',
            'house_number',
            'street',
            'state',
            'city',
            'country',
            'zipcode',
        ]));

        return true;
    }

    protected function createNewPlace(string $label, array $inputs, bool $private = false): Place
    {
        $inputs = Arr::only($inputs, [
            'type',
            'house_number',
            'street',
            'state',
            'city',
            'country',
            'zipcode',
        ]);

        return $this->ownedPlaces()->create(array_merge($inputs, [
            'label' => $this->determinePlaceLabel($label),
            'is_private' => $private,
        ]));
    }

    public function addPlace(string $label, array $inputs, bool $private = true)
    {
        $newPlace = $this->createNewPlace($label, $inputs, $private);

        return $this->switchPlace($newPlace);
    }

    public function insertPlace(string $label, array $inputs, bool $private = true): Place
    {
        return $this->createNewPlace($label, $inputs, $private);
    }

    public function addPublicPlace(array $inputs)
    {
        return $this->createNewPlace(Place::PUBLIC, $inputs, false);
    }

    private function determinePlaceLabel($label)
    {
        $count = $this->ownedPlaces()
            ->where('label', $label)
            ->count();

        if ($count > 1) {
            return "{$label} ({$count})";
        }

        return $label;
    }

    public function currentPlace()
    {
        if ($this->ownedPlaces->isNotEmpty() && !$this->current_place_id) {
            $this->switchPlace($this->ownedPlaces->first());
        }

        return $this->belongsTo(Place::class, 'current_place_id');
    }

    public function switchPlace($place)
    {
        if (!$this->ownsPlace($place)) {
            return false;
        }

        if (!$this->isCurrentPlace($place)) {
            $this->forceFill([
                'current_place_id' => $place->id,
            ])->save();

            $this->setRelation('currentPlace', $place);
        }

        return true;
    }

    public function ownsPlace($place)
    {
        if (is_null($place)) {
            return false;
        }

        return $this->ownedPlaces->contains(fn ($p) => $p->id === $place->id);
    }

    public function ownedPlaces()
    {
        return $this->morphMany(Place::class, 'owner');
    }

    public function updateOrCreatePlace(string $label, array $inputs)
    {
        $place = $this->currentPlace;

        if (!$place) {
            return $this->addPlace($label, $inputs);
        }

        return $this->updatePlace($place, $inputs);
    }
}
