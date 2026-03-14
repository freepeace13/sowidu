<?php

namespace App\Http\Controllers\Json\Addressbook\Person;

use App\Http\Controllers\Json\Addressbook\BaseController;
use App\Models\User as Person;
use App\Transformers\Json\PersonTransformer;
use Illuminate\Http\Request;

class NewPersonSearch extends BaseController
{
    public function __invoke(Request $request)
    {
        $text = $request->query('text');
        $size = $request->query('size', 10);

        $people = $this->createServiceInstance()->getPeopleIds();

        $excludeIds = $people
            ->unless(
                $this->isImpersonating(),
                fn ($people) => $people->push($request->user()->id),
            )
            ->toArray();

        return PersonTransformer::collection(
            Person::search($text)
                ->whereNotIn('id', $excludeIds)
                ->limit($size)
                ->get(),
        );
    }
}
