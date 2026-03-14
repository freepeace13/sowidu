<?php

use App\Http\Controllers\Json\Addressbook\AddressbookController;
use App\Http\Controllers\Json\Addressbook\Organization\NewOrganizationMemberSearch;
use App\Http\Controllers\Json\Addressbook\Organization\NewOrganizationSearch;
use App\Http\Controllers\Json\Addressbook\Person\NewPersonOrganizationSearch;
use App\Http\Controllers\Json\Addressbook\Person\NewPersonSearch;
use Illuminate\Support\Facades\Route;

Route::get('/addressbook/organization/{id}/members/new', NewOrganizationMemberSearch::class)
    ->name('json.addressbook.organization.member.new');

Route::get('/addressbook/organization/new', NewOrganizationSearch::class)
    ->name('json.addressbook.organization.new');

Route::get('/addressbook/person/new', NewPersonSearch::class)
    ->name('json.addressbook.person.new');

Route::get('/addressbook/person/{id}/organizations/new', NewPersonOrganizationSearch::class)
    ->name('json.addressbook.person.organization.new');

Route::post('/addressbook', [AddressbookController::class, 'store'])
    ->name('json.addressbook.store');

Route::get('/addressbook/{id}', [AddressbookController::class, 'show'])
    ->name('json.addressbook.show')
    ->whereNumber('id');

Route::put('/addressbook/{id}', [AddressbookController::class, 'update'])
    ->name('json.addressbook.update')
    ->whereNumber('id');

Route::get('/addressbook/{resource?}', [AddressbookController::class, 'index'])
    ->name('json.addressbook.index')
    ->whereAlpha('resource');

Route::get('/addressbook/careOf/search', [AddressbookController::class, 'careof'])->name('json.addressbook.careof');
