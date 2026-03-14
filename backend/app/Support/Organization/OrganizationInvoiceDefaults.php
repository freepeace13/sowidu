<?php

namespace App\Support\Organization;

use App\Models\Company;
use App\Transformers\EmployeeTransformer;
use Illuminate\Support\Arr;

class OrganizationInvoiceDefaults
{
    const STORE_KEY = 'invoice_defaults';

    public function __construct(protected Company $company) {}

    public function saveDefaults()
    {
        $this->save($this->defaults());
    }

    protected function save(array $values)
    {
        // Update managing_director values
        $managingDirectorId = data_get($values, 'managing_director.id')
            ?? data_get($values, 'managing_director');

        if (filled($managingDirectorId)) {
            data_set(
                $values,
                'managing_director',
                EmployeeTransformer::make($this->company->employees()
                    ->find($managingDirectorId))->resolve(),
            );
        }

        $settings = $this->company->settings;

        Arr::set($settings, self::STORE_KEY, $values);

        return $this->company->forceFill([
            'settings' => $settings,
        ])
            ->save();
    }

    protected function defaults(): array
    {
        return [
            'managing_director' => null,
            'website' => null,
            'company_email' => null,
            'commercial_register' => null,
            'commercial_register_number' => null,
            'payment_terms' => 10,
            'payment_terms_instructions' => null,
        ];
    }

    public function all(): array
    {
        return collect($this->company->settings[self::STORE_KEY] ?? $this->defaults())->toArray();
    }

    public function get(string $keyword, $default = null): array|string|null
    {
        return Arr::get($this->all(), $keyword, $default);
    }

    public function has(string $key): bool
    {
        return Arr::has($this->all(), $key);
    }

    public function update(array $newValues)
    {
        $newValues = Arr::only($newValues, array_keys($this->defaults()));

        $newValues = collect($this->defaults())->mapWithKeys(function ($value, $key) use ($newValues) {
            return [$key => $newValues[$key] ?? $value];
        })
            ->toArray();

        $updatedSettings = collect($this->all())
            ->merge($newValues)
            ->toArray();

        return $this->save($updatedSettings);
    }
}
