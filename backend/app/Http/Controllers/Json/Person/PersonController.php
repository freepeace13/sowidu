<?php

namespace App\Http\Controllers\Json\Person;

use App\Http\Controllers\Json\BaseController;
use App\Models\User as Person;
use App\Transformers\Json\PersonTransformer;
use Illuminate\Http\Request;

class PersonController extends BaseController
{
    public function index(Request $request)
    {
        $result = Person::search($request->keyword)->limit(5)->get();

        return $this->json(
            $result->map(fn ($resource) => (new PersonTransformer($resource))->resolve()),
        );
    }

    public function show($id)
    {
        $result = Person::findOrFail($id);

        return $this->json(
            (new PersonTransformer($result))
                ->withAddress()
                ->resolve(),
        );
    }
}
