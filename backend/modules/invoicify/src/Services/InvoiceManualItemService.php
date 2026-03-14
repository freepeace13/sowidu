<?php

namespace Modules\Invoicify\Services;

use App\Models\CatalogItemUnit;
use App\Models\Company;
use App\Models\User;
use App\Services\CompanyService;
use Modules\Invoicify\Models\InvoiceManualItem;

class InvoiceManualItemService
{
    public function __construct(protected User $user, protected Company $company) {}

    public static function make(User $user, Company $company): static
    {
        return new static($user, $company);
    }

    public function create(array $data)
    {
        // Add unit_name from `unit`
        $unit = data_get($data, 'unit');
        $unitName = $this->getUnitName($unit);
        data_set($data, 'unit_name', $unitName);

        $invoiceManualItem = InvoiceManualItem::make($data);

        $invoiceManualItem->user()
            ->associate($this->user);
        $invoiceManualItem->company()
            ->associate($this->company);
        $invoiceManualItem->save();

        return tap($invoiceManualItem)->update([
            'vendor_id' => $this->generateVendorId(),
            'internal_id' => $this->generateInternalId($invoiceManualItem->id),
        ]);
    }

    public function delete(int $manualItemId)
    {
        $invoiceManualItem = InvoiceManualItem::findOrFail($manualItemId);

        throw_unless(
            $invoiceManualItem->isOwnedByCompany($this->company),
            new \Exception('Invoice manual item not found'),
        );

        $invoiceManualItem->delete();
    }

    protected function getUnitName(int $unitId): string
    {
        $unit = CatalogItemUnit::find($unitId);

        if (blank($unit)) {
            throw new \Exception('Unit not found');
        }

        return $unit->name;
    }

    protected function generateVendorId(): string
    {
        $companyInitial = CompanyService::make($this->company)->getCompanyInitial();

        return "$companyInitial-" . crc32($this->company->getKey() + 1000);
    }

    protected function generateInternalId(int $manualItemId): string
    {
        return 'MI-' . crc32($manualItemId);
    }
}
