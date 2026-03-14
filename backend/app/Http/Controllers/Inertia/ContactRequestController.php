<?php

namespace App\Http\Controllers\Inertia;

use App\Enums\ContactType;
use App\Support\Facades\Impersonate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Packages\Contacts\Actions\AcceptRequestAction;
use Packages\Contacts\Actions\AddContactAction;
use Packages\Contacts\Actions\CancelRequestAction;
use Packages\Contacts\Actions\DenyRequestAction;

class ContactRequestController extends InertiaController
{
    public function index()
    {
        return Inertia::render('Contacts/Requests', [
            'title' => 'Contacts',
        ]);
    }

    public function store(Request $request)
    {
        $recipient = $this->resolveRecipient($request);

        return \DB::transaction(function () use ($recipient) {
            AddContactAction::make(Impersonate::user())->execute($recipient);

            return back(303);
        });
    }

    public function accept(Request $request)
    {
        $sender = $this->resolveSender($request);

        return \DB::transaction(function () use ($sender) {
            AcceptRequestAction::make(Impersonate::user())->execute($sender);

            return back(303);
        });
    }

    public function deny(Request $request)
    {
        $sender = $this->resolveSender($request);

        return \DB::transaction(function () use ($sender) {
            DenyRequestAction::make(Impersonate::user())->execute($sender);

            return back(303);
        });
    }

    public function destroy(Request $request)
    {
        $recipient = $this->resolveRecipient($request);

        return \DB::transaction(function () use ($recipient) {
            CancelRequestAction::make(Impersonate::user())->execute($recipient);

            return back(303);
        });
    }

    private function resolveRecipient(Request $request)
    {
        if (!$modelClass = ContactType::getValue($request->recipient_type)) {
            throw ValidationException::withMessages([
                'recipient' => ['Invalid recipient data'],
            ]);
        }

        return $modelClass::findOrFail($request->recipient_id);
    }

    private function resolveSender(Request $request)
    {
        if (!$modelClass = ContactType::getValue($request->sender_type)) {
            throw ValidationException::withMessages([
                'sender' => ['Invalid sender data'],
            ]);
        }

        return $modelClass::findOrFail($request->sender_id);
    }
}
