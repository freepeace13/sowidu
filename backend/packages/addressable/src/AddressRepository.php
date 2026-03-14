<?php

namespace Packages\Addressable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Packages\Addressable\Models\Address;

class AddressRepository
{
    protected $subject;

    public function all(): Collection
    {
        return $this->getQuery()->latest()->get();
    }

    public function findByName(string $name): ?Address
    {
        return $this->getQuery()->firstWhere('name', $name);
    }

    public function newestFirst(): ?Address
    {
        return $this->getQuery()->latest()->first();
    }

    public function oldestFirst(): ?Address
    {
        return $this->getQuery()->oldest()->first();
    }

    public function create(array $data): Address
    {
        $address = (new Address)->fill($data);

        $this->subject->addresses()->save($address);

        return $address;
    }

    public function add(string $name, array $data): Address
    {
        return $this->create(array_merge($data, compact('name')));
    }

    public function deleteAll(): bool
    {
        return $this->getQuery()->delete();
    }

    public function delete($address): bool
    {
        if (is_string($address)) {
            return $this->getQuery()
                ->where('name', $address)
                ->delete();
        }

        if (is_int($address)) {
            return $this->getQuery()
                ->where('id', $address)
                ->delete();
        }

        return false;
    }

    public function setSubject(Model $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    protected function getQuery()
    {
        return $this->subject->addresses();
    }
}
