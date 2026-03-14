<?php

namespace Modules\Invoicify\Tests\Feature\Controllers;

use App\Models\CatalogItemUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\Invoicify\Models\Invoice;
use Tests\TestCase;

class StoreManualItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::define('manageManualItems', fn () => true);
        Gate::before(function ($user, $ability) {
            return true;
        });
    }

    public function test_stores_manual_item_successfully()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);
        $invoice = $this->createInvoice($company);
        $unit = $this->createUnit();

        $data = [
            'name' => 'Test Manual Item',
            'type' => 'service',
            'quantity' => 2,
            'unit' => $unit->id,
            'selling_price' => 100.50,
            'description' => 'Test description',
        ];

        $response = $this->post(route('invoicify.manual_items.store', $invoice), $data);

        $response->assertRedirect();
        $response->assertSessionHas('flash');

        $this->assertDatabaseHas('invoice_manual_items', [
            'name' => 'Test Manual Item',
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'name' => 'Test Manual Item',
        ]);
    }

    public function test_validates_required_fields()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);
        $invoice = $this->createInvoice($company);

        $response = $this->post(route('invoicify.manual_items.store', $invoice), []);

        // ValidationException is thrown and handled by Laravel's exception handler
        // which sets session errors and redirects back
        $response->assertSessionHasErrors(['name', 'type', 'quantity', 'unit', 'selling_price', 'description']);
    }

    public function test_requires_authentication()
    {
        $invoice = $this->createInvoice($this->createCompany());

        $response = $this->post(route('invoicify.manual_items.store', $invoice), []);

        $response->assertStatus(302);
    }

    protected function createUser()
    {
        return \App\Models\User::factory()->create();
    }

    protected function createCompany()
    {
        return \App\Models\Company::factory()->create();
    }

    protected function createInvoice($company)
    {
        return Invoice::factory()->create([
            'company_id' => $company->id,
        ]);
    }

    protected function createUnit()
    {
        return CatalogItemUnit::factory()->create();
    }
}
