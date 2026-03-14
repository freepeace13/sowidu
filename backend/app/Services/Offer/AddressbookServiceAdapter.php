<?php

namespace App\Services\Offer;

use App\Models\Addressbook;
use App\Modules\Addressbook\AddressbookHelper;
use App\Transformers\Addressbook\AddressbookTransformer;
use Illuminate\Database\Eloquent\Model;
use Modules\Offer\Contracts\External\AddressbookServiceContract;

class AddressbookServiceAdapter implements AddressbookServiceContract
{
    public function __construct(
        protected AddressbookHelper $addressbookHelper,
    ) {}

    public function find(int $id): ?Model
    {
        return Addressbook::find($id);
    }

    public function findOrFail(int $id): Model
    {
        return Addressbook::findOrFail($id);
    }

    public function getDisplayName(Model $addressbook): string
    {
        /** @var Addressbook $addressbook */
        return $this->addressbookHelper->getDisplayName($addressbook);
    }

    public function getEmail(Model $addressbook): ?string
    {
        /** @var Addressbook $addressbook */
        return $addressbook->email;
    }

    public function getPrimaryContactEmail(Model $addressbook): ?string
    {
        /** @var Addressbook $addressbook */
        return $this->addressbookHelper->getPrimaryContactEmail($addressbook);
    }

    public function hasEmail(Model $addressbook): bool
    {
        /** @var Addressbook $addressbook */
        return !empty($addressbook->email) || !empty($this->getPrimaryContactEmail($addressbook));
    }

    public function findUserByEmail(string $email): ?Model
    {
        return $this->addressbookHelper->findUser($email);
    }

    public function getAddressbookIdsFromUser(Model $user): \Illuminate\Support\Collection
    {
        return $this->addressbookHelper->getAddressbookIdsFromUser($user);
    }

    public function transformWithAddress(Model $addressbook): array
    {
        /** @var Addressbook $addressbook */
        return (new AddressbookTransformer($addressbook))
            ->withAddress()
            ->resolve();
    }

    public function isForeignOrganization(Model $addressbook): bool
    {
        /** @var Addressbook $addressbook */
        return $addressbook->isForeignOrganization();
    }
}
