<?php

namespace Packages\Countries\Data;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Packages\Countries\Exceptions\CountryException;
use Packages\Data\Data;
use PragmaRX\Countries\Package\Countries;

class Country extends Data
{
    protected $fillable = [
        'cca2',
        'flag',
        'name',
        'continent',
    ];

    public static function all()
    {
        return self::collection(Countries::all());
    }

    public static function find($code)
    {
        return Cache::remember(
            "country.code.$code",
            now()->addDay(),
            fn () => self::from(
                Countries::where('cca2', $code)->first(),
            ),
        );
    }

    public static function findOrFail($code)
    {
        if (!$country = self::find($code)) {
            throw CountryException::notFound($code);
        }

        return $country;
    }

    public function states()
    {
        return rescue(fn () => State::collection(
            Countries::all()
                ->firstWhere('cca2', $this->cca2)
                ->hydrateStates()
                ->states,
        ), collect([]), false);
    }

    public function cities()
    {
        return rescue(fn () => City::collection(
            Countries::all()
                ->firstWhere('cca2', $this->cca2)
                ->hydrateCities()
                ->cities,
        ), collect([]), false);
    }

    public static function from($payload): Country
    {
        return new self([
            'cca2' => Arr::get($payload, 'cca2'),
            'name' => Arr::get($payload, 'name.common'),
            'flag' => Arr::get($payload, 'flag.emoji'),
            'continent' => Arr::get($payload, 'geo.continent'),
        ]);
    }

    public static function getCountryCode($name)
    {
        return self::from(
            Countries::all()->firstWhere('name.common', $name),
        );
    }
}
