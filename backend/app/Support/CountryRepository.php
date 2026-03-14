<?php

namespace App\Support;

use Illuminate\Support\Arr;
use PragmaRX\Countries\Package\Countries;
use PragmaRX\Countries\Package\Services\Config;

class CountryRepository
{
    protected $countries;

    public function __construct(Countries $countries)
    {
        $this->countries = $countries;
    }

    public static function make(array $options = [])
    {
        $defaults = [
            'currencies' => true,
            'flag' => false,
            'timezones' => false,
        ];

        $config = new Config([
            'hydrate' => ['elements' => array_merge($defaults, $options)],
        ]);

        return new static(new Countries($config));
    }

    public function getCurrencies()
    {
        $currencies = $this->countries->all()
            ->map(function ($country) {
                return $country->currencies
                    ->values()
                    ->first()
                    ->toArray();
            })
            ->toArray();

        return collect(array_values($currencies))
            ->map(function ($currency) {
                return [
                    'name' => Arr::get($currency, 'name'),
                    'iso' => Arr::get($currency, 'iso'),
                    'symbol' => Arr::get($currency, 'units.major.symbol'),
                ];
            })
            ->unique(function ($currency) {
                return $currency['iso']['code'];
            });
    }

    public function getCountryCodes()
    {
        return $this->countries->all()->mapWithKeys(function ($country) {
            return [
                $country->cca2 => [
                    'name' => $country->name->common,
                    'cca3' => $country->cca3,
                    'currency' => $country->currencies->first()->toArray(),
                ],
            ];
        })->toArray();
    }
}
