<?php

namespace App\Http\Controllers\Inertia\Auth;

use App\Actions\Auth\CreateNewUser;
use App\Models\Addressbook;
use App\Traits\WithSnackbar;
use Crypt;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RegisterController extends Controller
{
    use WithSnackbar;

    public function create(Request $request)
    {
        return Inertia::render('Auth/Register', [
            'title' => 'Register',
            'metadata' => $this->parseMetaData($request->get('metadata')),
        ]);
    }

    protected function parseMetaData($metaData = null): ?array
    {
        if (!$metaData) {
            return null;
        }

        $addressbookId = Crypt::decrypt($metaData);
        $addressbook = Addressbook::find($addressbookId, ['id', 'name', 'email', 'details']);

        return [
            'first_name' => $addressbook->details['first_name'] ?? Str::of($addressbook->name)->before(' ')->__toString(),
            'last_name' => $addressbook->details['last_name'] ?? Str::of($addressbook->name)->after(' ')->__toString(),
            'email' => $addressbook->email,
            'metadata' => $metaData,
        ];
    }

    public function store(Request $request, CreateNewUser $creator)
    {
        $creator->create($request->all());

        return back(303)->with('flash', [
            'type' => 'success',
            'message' => 'Confirm your email address to get started.',
        ]);
    }
}
