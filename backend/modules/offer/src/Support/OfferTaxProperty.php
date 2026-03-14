<?php

namespace Modules\Offer\Support;

use Modules\Offer\Models\Offer;

class OfferTaxProperty
{
    protected $key = 'taxes';

    protected \Illuminate\Support\Collection $property;

    protected bool $toCollection = false;

    public function __construct(protected Offer $offer)
    {
        $this->property = collect(data_get($offer->properties, $this->key, []));
    }

    public function toCollection(): self
    {
        $this->toCollection = true;

        return $this;
    }

    public function all(): array|\Illuminate\Support\Collection
    {
        if ($this->toCollection) {
            return $this->property;
        }

        return $this->property->toArray();
    }

    public function attach(array $tax): bool
    {
        // Validate `$tax` has `name` and `rate` keys
        throw_validation_unless(
            array_key_exists('name', $tax) && array_key_exists('rate', $tax) && array_key_exists('id', $tax),
            'Tax must have name and rate keys.',
        );

        // If `id` is already in the property, merge the tax with the existing tax
        if ($this->property->contains('id', $tax['id'])) {
            // Exists, merge the tax with the existing tax
            $taxes = $this->property->map(function ($property) use ($tax) {
                if ($property['id'] === $tax['id']) {
                    return array_merge($property, $tax);
                }

                return $property;
            })
                ->toArray();

            return $this->save($taxes);
        }

        // Not exists, add the tax to the property
        return $this->save(
            $this->property->push($tax)
                ->toArray(),
        );
    }

    protected function save(array $taxes): bool
    {
        $taxPayload = [$this->key => $taxes];

        if (blank($this->offer->properties)) {
            $this->offer->properties = [$this->key => $taxes];
        }

        if (filled($this->offer->properties)) {
            $this->offer->properties = $this->offer->properties
                ->merge($taxPayload);
        }

        return $this->offer->save();
    }
}
