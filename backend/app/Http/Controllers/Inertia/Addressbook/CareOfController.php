<?php

namespace App\Http\Controllers\Inertia\Addressbook;

use App\Http\Controllers\Controller;
use App\Models\Addressbook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CareOfController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AddressBook $addressbook): RedirectResponse
    {
        $validated = $request->validate([
            'cos' => ['array'],
            'cos.*' => ['numeric'],
        ]);

        $addressbook->careOfs()->sync($validated['cos']);

        flash_success('Successfully add care ofs');

        return back();
    }
}
