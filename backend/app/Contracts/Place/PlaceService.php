<?php

namespace App\Contracts\Place;

use App\Models\Country;
use App\Models\Place;
use Illuminate\Support\Collection;
use Packages\Data\DataCollection;

interface PlaceService
{
    public function distinct(): self;

    public function createCountry(string $name): Country;

    public function findCountry(string|int $countryCode);

    public function findCountryCode(string $countryCode);

    public function getCountries(?string $keyword = null): Collection;

    public function getCountriesFromLibrary();

    public function getCountryStates($code, ?string $keyword = null): Collection;

    public function getCountryCities($code, ?string $keyword): Collection;

    public function getCountryStatesFromLibrary(string $code): DataCollection|Collection;

    public function getCountryCitiesFromLibrary(string $code): DataCollection|Collection;

    public function getCountryStateCacheKey(string $code): string;

    public function getCountryCityCacheKey(string $code): string;

    public function getSimilarHouseNumbers($keyword);

    public function getSimilarStreets($keyword);

    public function getSimilarZipcodes($keyword);

    public function getSimilarField($field, $keyword, int $limit);

    public function getList(array $filters, ?int $perPage);

    public function getGoogleMapUrl(Place $place): string;
}
