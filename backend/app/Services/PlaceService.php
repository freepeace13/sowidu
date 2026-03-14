<?php

namespace App\Services;

use App\Contracts\Place\PlaceService as PlaceServiceContract;
use App\Models\City;
use App\Models\Country;
use App\Models\Place;
use App\Models\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Packages\Countries\Countries;
use Packages\Data\DataCollection;

class PlaceService implements PlaceServiceContract
{
    protected bool $distinctResults = false;

    public function distinct(): self
    {
        $this->distinctResults = true;

        return $this;
    }

    public function createCountry(string $name): Country
    {
        return Country::updateOrCreate(['name' => ucfirst(strtolower($name))]);
    }

    public function findCountry(string|int $countryCode)
    {
        if ($country = $this->findCountryCode($countryCode)) {
            return $country;
        }

        // Not found on country library - Check on `Country` model
        return Country::query()->find($countryCode);
    }

    public function findCountryCode(string $countryCode)
    {
        $country = Countries::find($countryCode);

        return $country?->cca2 !== null && $country?->name !== null ? $country : null;
    }

    public function getCountries(?string $keyword = null): Collection
    {
        $libraryCountries = $this->getCountriesFromLibrary();

        return Country::query()
            ->when($keyword, fn ($query) => $query->where('name', 'like', "%$keyword%"))
            ->get(['name', 'id'])
            ->concat($libraryCountries)
            ->map(fn ($country) => [
                'name' => $country->name,
                'code' => $country->cca2,
                'flag' => $country->flag,
            ])
            ->values();
    }

    public function getCountriesFromLibrary()
    {
        return Cache::remember(
            'country.list',
            now()->addDay(),
            fn () => Countries::all(),
        );
    }

    public function getCountryStates($code, ?string $keyword = null): Collection
    {
        $defaults = $this->getCountryStatesFromLibrary($code);

        return State::query()
            ->when($keyword, fn ($query) => $query->where('name', 'like', "%$keyword%"))
            ->get('name')
            ->pluck('name')
            ->concat($defaults)
            ->values();
    }

    public function getCountryCities($code, ?string $keyword): Collection
    {
        $defaults = $this->getCountryCitiesFromLibrary($code);

        return City::query()
            ->when($keyword, fn ($query) => $query->where('name', 'like', "%$keyword%"))
            ->get('name')
            ->pluck('name')
            ->concat($defaults)
            ->values();
    }

    public function getCountryStatesFromLibrary(string $code): DataCollection|Collection
    {
        return Cache::remember(
            $this->getCountryStateCacheKey($code),
            now()->addDay(),
            fn () => Countries::find($code)
                ->states()
                ->pluck('name')
                ->values(),
        );
    }

    public function getCountryCitiesFromLibrary(string $code): DataCollection|Collection
    {
        return Cache::remember(
            $this->getCountryCityCacheKey($code),
            now()->addDay(),
            fn () => Countries::find($code)
                ->cities()
                ->pluck('name')
                ->values(),
        );
    }

    public function getCountryStateCacheKey(string $code): string
    {
        return "place.country.$code.states";
    }

    public function getCountryCityCacheKey(string $code): string
    {
        return "place.country.$code.cities";
    }

    public function getSimilarHouseNumbers($keyword)
    {
        return $this->getSimilarField('house_number', $keyword);
    }

    public function getSimilarStreets($keyword)
    {
        return $this->getSimilarField('street', $keyword);
    }

    public function getSimilarZipcodes($keyword)
    {
        return $this->getSimilarField('zipcode', $keyword);
    }

    public function getSimilarField($field, $keyword, int $limit = 5)
    {
        $result = Place::select($field)
            ->distinct()
            ->when(filled($keyword), function ($query) use ($field, $keyword) {
                $query->where($field, 'LIKE', "%{$keyword}%");
            })
            ->limit($limit)
            ->get();

        return $result->pluck($field);
    }

    public function getList(array $filters = [], ?int $perPage = null)
    {
        return Place::query()
            ->when(
                $q = $filters['q'] ?? $filters['text'] ?? null,
                fn ($query) => $query->searchFullAddress($q),
            )
            ->when(
                $this->distinctResults,
                fn ($query) => $query->distinctFullAddress(),
            )
            ->when(
                $size = $filters['size'] ?? null,
                fn ($query) => $query->limit($size),
            )
            ->when(
                $perPage,
                function (Builder $query) use ($perPage) {
                    if ($perPage == -1) {
                        $results = $query->get();

                        return new LengthAwarePaginator(
                            $results,
                            $results->count(),
                            $perPage,
                        );
                    }

                    return $query->paginate($perPage);
                },
                fn ($query) => $query->get(),
            );
    }

    public function getGoogleMapUrl(Place $place): string
    {
        $code = implode('+', array_filter([
            $place->street,
            $place->house_number,
            $place->zipcode,
            $place->city,
        ]));

        return "https://www.google.de/maps/place/{$code}";
    }
}
