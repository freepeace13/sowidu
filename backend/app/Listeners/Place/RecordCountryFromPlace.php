<?php

namespace App\Listeners\Place;

use App\Events\Place\PlaceSaved;
use App\Models\Country;
use App\Services\PlaceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecordCountryFromPlace implements ShouldQueue
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
     * @return void
     */
    public function handle(PlaceSaved $event)
    {
        $place = $event->place;

        // Get country from `place`
        $countryCode = $place->country;

        // Check if city is on the library list or on `City` model
        if ($this->service->findCountry($countryCode)) {
            return;
        }

        // Not exists - create!
        Country::updateOrCreate([
            'name' => $place->country_name,
        ]);
    }
}
