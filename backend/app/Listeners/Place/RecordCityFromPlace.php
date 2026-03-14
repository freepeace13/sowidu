<?php

namespace App\Listeners\Place;

use App\Events\Place\PlaceSaved;
use App\Models\City;
use App\Services\PlaceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class RecordCityFromPlace implements ShouldQueue
{
    use InteractsWithQueue;

    public $afterCommit = true;

    public function __construct(protected PlaceService $service)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\Place\PlaceSaved
     * @return void
     */
    public function handle(PlaceSaved $event)
    {
        $place = $event->place;

        // Get city from `place`
        $city = $place->city;
        $countryCode = $place->country;

        // Check if city is on the library list or on `City` model
        if (
            $this->service
                ->getCountryCitiesFromLibrary($countryCode)
                ->contains($city)
            || City::whereName($city)->whereCountryCode($countryCode)->exists()
        ) {
            return;
        }

        // Not exists - create!
        City::updateOrCreate([
            'name' => $city,
        ], [
            'country_code' => $countryCode,
        ]);

        Cache::forget($this->service->getCountryCityCacheKey($countryCode));
    }
}
