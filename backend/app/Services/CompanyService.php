<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeRate;
use App\Models\User;

class CompanyService
{
    public function __construct(protected Company $company) {}

    public static function make(Company $company)
    {
        return new static($company);
    }

    public function getEmployeeFromUser(User $user): Employee
    {
        return $this->company->getEmployee($user);
    }

    public function getEmployeeRateFromUser(User $user): ?EmployeeRate
    {
        return $this->getEmployeeFromUser($user)
            ?->rate ?? $this->defaultEmployeeRate();
    }

    protected function defaultEmployeeRate()
    {
        return EmployeeRate::make([
            'rate' => config('app.default.organization.settings.employee_rate.rate'),
            'currency' => config('app.default.organization.settings.employee_rate.currency'),
        ]);
    }

    public function getCompanyInitial(): string
    {
        return pluck_initials($this->company->username);
    }

    public function getEmployeeCode(User $user): int
    {
        return crc32(
            $this->getEmployeeFromUser($user)
                ->id,
        );
    }

    public function getEmployeeUuid(User $user): string
    {
        return $this->getEmployeeFromUser($user)->uuid;
    }

    public function getCompanyCurrency(): ?string
    {
        return $this->company->currency;
    }

    public function currency(): array
    {
        $currency = $this->getCompanyCurrency();

        if (blank($currency)) {
            $currency = config('app.default.currency');
        }

        return [
            'name' => $currency,
            'symbol' => currency_symbol($currency),
        ];
    }

    public function currencyName(): string
    {
        return data_get($this->currency(), 'name');
    }

    public function getPaymentTerms(): ?int
    {
        return (int) $this->company->settings()
            ->invoiceDefaults()
            ->get('payment_terms');
    }

    public function getLegalForm(): string
    {
        return $this->company->loadMissing('legalForm')->legalForm->legal_form;
    }
}
