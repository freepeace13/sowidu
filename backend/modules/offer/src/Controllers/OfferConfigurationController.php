<?php

namespace Modules\Offer\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Offer\Actions\Company\SaveCompanyOfferConfiguration;
use Modules\Offer\Http\Controllers\Controller;
use Modules\Offer\Transformers\CompanyOfferConfigurationTransformer;

class OfferConfigurationController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Account/Organization/Settings/OfferConfiguration', [
            'configuration' => CompanyOfferConfigurationTransformer::make($request->company()->offerConfiguration)
                ->resolve(),
        ]);
    }

    public function update(Request $request)
    {
        SaveCompanyOfferConfiguration::run(
            $request->user(),
            $request->company(),
            $request->all(),
        );

        flash_success(__('offer.messages.configuration.updated'));

        return back(303);
    }
}
