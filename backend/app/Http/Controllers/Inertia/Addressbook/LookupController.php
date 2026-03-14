<?php

namespace App\Http\Controllers\Inertia\Addressbook;

use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Company as Team;
use App\Models\Employee as TeamMember;
use App\Models\User as Person;
use App\Transformers\Addressbook\LookupResultTransformer;
use Illuminate\Http\Request;

class LookupController extends InertiaController
{
    public function __invoke(Request $request)
    {
        $results = [];

        $results['People'] = $this->getSearchResults(Person::class, $request);
        $results['Teams'] = $this->getSearchResults(Team::class, $request);
        $results['Team Members'] = $this->getSearchResults(TeamMember::class, $request);

        return response()->json(['data' => $results]);
    }

    protected function getSearchResults($resource, $request)
    {
        return $resource::search($request->keyword)
            ->paginate(10)
            ->through(function ($item) {
                return (new LookupResultTransformer($item))->resolve();
            });
    }
}
