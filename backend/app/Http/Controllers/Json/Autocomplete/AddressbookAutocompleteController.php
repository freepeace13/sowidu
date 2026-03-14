<?php

namespace App\Http\Controllers\Json\Autocomplete;

use App\Http\Controllers\Json\BaseController;
use App\Services\AddressbookService;
use App\Transformers\Addressbook\AddressbookTransformer;
use Illuminate\Http\Request;

class AddressbookAutocompleteController extends BaseController
{
    public function __invoke(Request $request)
    {
        return $this->json(
            AddressbookService::make($request->user(), $request->company())
                ->filters($request->only(['q', 'id']))
                ->limit($request->get('limit', 10))
                ->get()
                ->map(
                    fn ($addressbook) => (new AddressbookTransformer($addressbook))
                        ->withCareOfs() // TODO - make this dynamic on request!
                        ->resolve(),
                ),
        );
    }
}
