<?php

namespace Modules\Invoicify\Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\Invoicify\Models\Invoice;
use Tests\TestCase;

class InvoicifyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::define('update', fn () => true);
        Gate::before(function ($user, $ability) {
            return true;
        });
    }

    public function test_updates_invoice_successfully()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);
        $invoice = $this->createInvoice($company);

        $data = [
            'notes' => 'Updated notes',
        ];

        $response = $this->patch(route('invoicify.update', $invoice), $data);

        $response->assertRedirect();
        $response->assertSessionHas('flash');

        $invoice->refresh();
        $this->assertEquals('Updated notes', $invoice->notes);
    }

    public function test_requires_authentication()
    {
        $invoice = $this->createInvoice($this->createCompany());

        $response = $this->patch(route('invoicify.update', $invoice), []);

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
}
