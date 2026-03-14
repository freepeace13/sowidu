<?php

namespace App\Listeners\Place;

use App\Events\Place\PlaceSaved;
use App\Models\State;
use App\Services\PlaceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class RecordStateFromPlace implements ShouldQueue
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
        // Get state from `place`
        $state = $place->state;
        $countryCode = $place->country;

        // Check if state is on the library list or on `State` model
        if (
            $this->service
                ->getCountryStatesFromLibrary($countryCode)
                ->contains($state)
            || State::whereName($state)->whereCountryCode($countryCode)->exists()
        ) {
            return;
        }

        // Not exists - create!
        State::updateOrCreate([
            'name' => $state ?? 'Bayern',
        ], [
            'country_code' => $countryCode,
        ]);

        Cache::forget($this->service->getCountryCityCacheKey($countryCode));
    }
}
