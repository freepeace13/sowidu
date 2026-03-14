<?php

namespace Modules\Invoicify\Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\Invoicify\Enums\InvoiceStatus;
use Modules\Invoicify\Models\Invoice;
use Tests\TestCase;

class MarkAsPaidControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::define('markAsPaid', fn () => true);
        // Override the policy's before method to allow all actions
        Gate::before(function ($user, $ability) {
            return true;
        });
    }

    public function test_marks_invoice_as_paid_successfully()
    {
        $user = $this->createUser();
        $company = $this->createCompany($user);
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);
        $invoice = $this->createInvoice($company, InvoiceStatus::SENT);

        $response = $this->post(route('invoicify.mark_as_paid', $invoice));

        $response->assertRedirect();

        $invoice->refresh();
        $this->assertEquals(InvoiceStatus::PAID, $invoice->status);
    }

    public function test_returns_error_when_invoice_is_not_sent()
    {
        $user = $this->createUser();
        $company = $this->createCompany($user);
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);
        $invoice = $this->createInvoice($company, InvoiceStatus::DRAFT);

        $response = $this->post(route('invoicify.mark_as_paid', $invoice));

        $response->assertSessionHas('flash');
        $flash = session('flash');
        $this->assertEquals('error', $flash['type']);
    }

    public function test_requires_authentication()
    {
        $user = $this->createUser();
        $invoice = $this->createInvoice($this->createCompany($user), InvoiceStatus::SENT);

        $response = $this->post(route('invoicify.mark_as_paid', $invoice));

        $response->assertStatus(302);
    }

    protected function createUser()
    {
        return \App\Models\User::factory()->create();
    }

    protected function createCompany($user = null)
    {
        $user = $user ?? $this->createUser();

        return \App\Models\Company::factory()->forUser($user)->create();
    }

    protected function createInvoice($company, InvoiceStatus $status)
    {
        return Invoice::factory()->create([
            'company_id' => $company->id,
            'status' => $status,
        ]);
    }
}
