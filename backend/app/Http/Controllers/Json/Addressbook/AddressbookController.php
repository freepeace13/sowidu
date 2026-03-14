<?php

namespace App\Http\Controllers\Json\Addressbook;

use App\Models\Addressbook;
use App\Transformers\Json\AddressbookTransformer;
use Illuminate\Http\Request;
use Packages\Urn\UrnManager;

class AddressbookController extends BaseController
{
    public function index(Request $request, $resource = null)
    {
        $text = $request->query('text');
        $size = $request->query('size', 10);

        $service = $this->createServiceInstance();

        if ($resource) {
            $model = UrnManager::resource($resource);
            $model = rescue(fn () => (new $model)->getMorphClass(), $resource, false);
            $service = $service->onlyType($model);
        }

        $result = $service
            ->matchesText($text)
            ->limit($size)
            ->get();

        if (!$text) {
            // If result is empty, return suggestions
            $result->when($result->isEmpty(), fn () => $service->organizations()->limit($size)->get());
        }

        return AddressbookTransformer::collection($result);
    }

    public function store(Request $request)
    {
        // @todo Use New Action Class

        // $addressbook = $creator->create(
        //     $request->user(),
        //     $request->urn,
        //     $this->getCurrentTeamId()
        // );

        // return AddressbookTransformer::make($addressbook);
    }

    public function update(Request $request, Addressbook $addressbook)
    {
        // @todo Use New Action Class

        // $addressbook = $updater->update(
        //     $request->user(),
        //     $addressbook,
        //     $request->all()
        // );

        // return AddressbookTransformer::make($addressbook);
    }

    public function show(Request $request, $id)
    {
        $addressbook = $this->createServiceInstance()
            ->when(
                $organization = $request->get('organization'),
                fn ($query) => $query->whereHas(
                    'organizations',
                    fn ($query) => $query->where('addressbook_organization_id', $organization),
                )->with('organizations'),
            )
            ->find($id);

        abort_unless((bool) $addressbook, 404);

        return AddressbookTransformer::make($addressbook)
            ->withOrganization($request->get('organization'))
            ->withAddress();
    }

    public function careof(Request $request)
    {
        $service = $this->createServiceInstance();

        return $service->matchesText($request->get('search'))
            ->orderBy('name')
            ->get()->map(fn ($address) => AddressbookTransformer::make($address)->withAddress());
    }
}
