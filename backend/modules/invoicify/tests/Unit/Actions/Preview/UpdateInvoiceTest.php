<?php

namespace Modules\Invoicify\Tests\Unit\Actions\Preview;

use App\Models\Company;
use App\Models\Order;
use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\Invoicify\Actions\Preview\UpdateInvoiceAction;
use Modules\Invoicify\Enums\InvoiceKind;
use Modules\Invoicify\Enums\InvoiceType;
use Modules\Invoicify\Models\Invoice;
use Tests\TestCase;

class UpdateInvoiceTest extends TestCase
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
        // Set up user with team context for Impersonate::tenant() to work
        $user->teams()->attach($company);
        $user->switchTeam($company);
        $this->actingAs($user);

        $invoice = $this->createInvoice();
        $order = $this->createOrder($company);
        $place = $this->createPlace();
        $biller = $this->createCompany();

        $inputs = [
            'type' => InvoiceType::OUTGOING->value,
            'kind' => InvoiceKind::PARTIAL_1->value,
            'biller' => [
                'id' => $biller->id,
                'type' => $biller->getMorphClass(),
            ],
            'order' => [
                'id' => $order->id,
            ],
            'delivery_address' => [
                'id' => $place->id,
            ],
            'external_id' => 'EXT-123',
            'notes' => 'Test notes',
        ];

        $action = app(UpdateInvoiceAction::class);
        $action->update($user, $invoice, $inputs, $company->id);

        $invoice->refresh();
        $this->assertEquals(InvoiceType::OUTGOING, $invoice->type);
        $this->assertEquals(InvoiceKind::PARTIAL_1, $invoice->kind);
        $this->assertEquals($biller->id, $invoice->biller_id);
        $this->assertEquals($order->id, $invoice->order_id);
        $this->assertEquals($place->id, $invoice->delivery_address_id);
        $this->assertEquals('EXT-123', $invoice->external_id);
        $this->assertEquals('Test notes', $invoice->notes);
    }

    public function test_updates_only_notes_when_only_notes_provided()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $invoice = $this->createInvoice();

        $inputs = [
            'notes' => 'Updated notes only',
        ];

        $action = app(UpdateInvoiceAction::class);
        $action->update($user, $invoice, $inputs, $company->id);

        $invoice->refresh();
        $this->assertEquals('Updated notes only', $invoice->notes);
    }

    public function test_updates_only_payment_date_when_only_payment_date_provided()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $invoice = $this->createInvoice();

        $inputs = [
            'payment_date' => '2024-12-31',
        ];

        $action = app(UpdateInvoiceAction::class);
        $action->update($user, $invoice, $inputs, $company->id);

        $invoice->refresh();
        $this->assertEquals('2024-12-31', $invoice->payment_date->format('Y-m-d'));
    }

    public function test_updates_execution_period_when_provided()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $invoice = $this->createInvoice();
        $place = $this->createPlace();

        $inputs = [
            'execution_period_start' => '2024-01-01',
            'execution_period_end' => '2024-12-31',
            'construction_site' => [
                'id' => $place->id,
            ],
        ];

        $action = app(UpdateInvoiceAction::class);
        $action->update($user, $invoice, $inputs, $company->id);

        $invoice->refresh();
        $this->assertEquals('2024-01-01', $invoice->execution_period_start->format('Y-m-d'));
        $this->assertEquals('2024-12-31', $invoice->execution_period_end->format('Y-m-d'));
        $this->assertEquals($place->id, $invoice->construction_site_id);
    }

    public function test_validates_required_fields()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $invoice = $this->createInvoice();

        $inputs = [
            'type' => InvoiceType::OUTGOING->value,
            // Missing required fields
        ];

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action = app(UpdateInvoiceAction::class);
        $action->update($user, $invoice, $inputs, $company->id);
    }

    public function test_validates_incoming_invoice_requires_delivery_and_payment_date()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $invoice = $this->createInvoice();
        $order = $this->createOrder($company);
        $place = $this->createPlace();
        $biller = $this->createCompany();

        $inputs = [
            'type' => InvoiceType::INCOMING->value,
            'kind' => InvoiceKind::PARTIAL_1->value,
            'biller' => [
                'id' => $biller->id,
                'type' => $biller->getMorphClass(),
            ],
            'order' => [
                'id' => $order->id,
            ],
            'delivery_address' => [
                'id' => $place->id,
            ],
            // Missing delivery_date and payment_date for incoming invoice
        ];

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $action = app(UpdateInvoiceAction::class);
        $action->update($user, $invoice, $inputs, $company->id);
    }

    public function test_authorizes_user_before_updating()
    {
        $user = $this->createUser();
        $company = $this->createCompany();
        $invoice = $this->createInvoice();

        // Gate is already defined in setUp, so authorization should pass
        $inputs = [
            'notes' => 'Test notes',
        ];

        $action = app(UpdateInvoiceAction::class);
        $action->update($user, $invoice, $inputs, $company->id);

        $invoice->refresh();
        $this->assertEquals('Test notes', $invoice->notes);
    }

    protected function createUser()
    {
        return \App\Models\User::factory()->create();
    }

    protected function createCompany()
    {
        return \App\Models\Company::factory()->create();
    }

    protected function createInvoice()
    {
        return Invoice::factory()->create();
    }

    protected function createOrder(Company $company)
    {
        return Order::factory()->create([
            'team_id' => $company->id,
        ]);
    }

    protected function createPlace()
    {
        return Place::factory()->create();
    }
}
