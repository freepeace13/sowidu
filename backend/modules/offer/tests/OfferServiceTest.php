<?php

namespace Modules\Offer\tests;

use App\Enums\OfferActionType;
use App\Enums\OfferStatus;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Offer\Models\Offer;
use Modules\Offer\OfferService;
use Modules\Offer\Support\OfferCalculationService;
use Tests\TestCase;

class OfferServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected Company $company;

    protected Offer $offer;

    protected OfferService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->offer = Offer::factory()->create([
            'company_id' => $this->company->id,
            'status' => OfferStatus::DRAFT,
        ]);
        $this->service = OfferService::make($this->offer);
    }

    /** @test */
    public function it_can_create_offer_service_instance(): void
    {
        $this->assertInstanceOf(OfferService::class, $this->service);
        // Test that the service is properly initialized with the offer
        $this->assertNotNull($this->service);
    }

    /** @test */
    public function it_can_add_item_to_offer(): void
    {
        $name = $this->faker->words(3, true);
        $price = $this->faker->randomFloat(2, 10, 1000);
        $description = $this->faker->sentence();
        $details = ['category' => 'materials', 'unit' => 'pieces'];
        $quantity = $this->faker->numberBetween(1, 10);

        $this->service->addItem($name, $price, $description, $details, $quantity);

        $this->assertDatabaseHas('offer_items', [
            'offer_id' => $this->offer->id,
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'quantity' => $quantity,
        ]);
    }

    /** @test */
    public function it_can_get_calculation_service(): void
    {
        $calculation = $this->service->calculation();

        $this->assertInstanceOf(OfferCalculationService::class, $calculation);
    }

    /** @test */
    public function it_can_save_offer_totals(): void
    {
        // Add some items first
        $this->service->addItem('Test Item 1', 100.00, 'Description 1', [], 2);
        $this->service->addItem('Test Item 2', 50.00, 'Description 2', [], 1);

        $savedOffer = $this->service->saveOfferTotals();

        $this->assertEquals(250.00, $savedOffer->subtotal);
        $this->assertEquals(250.00, $savedOffer->net_amount);
        $this->assertEquals(0.00, $savedOffer->total_vat);
        $this->assertEquals(250.00, $savedOffer->grand_total);
    }

    /** @test */
    public function it_can_log_action(): void
    {
        $this->service->logAction(OfferActionType::SENT, $this->user);

        $this->assertDatabaseHas('offer_histories', [
            'offer_id' => $this->offer->id,
            'author_id' => $this->user->id,
            'action_type' => OfferActionType::SENT,
        ]);
    }

    /** @test */
    public function it_can_accept_offer(): void
    {
        $this->service->accept();

        $this->offer->refresh();
        $this->assertEquals(OfferStatus::ACCEPTED, $this->offer->status);
    }

    /** @test */
    public function it_can_send_draft_offer(): void
    {
        $this->service->send();

        $this->offer->refresh();
        $this->assertEquals(OfferStatus::PENDING, $this->offer->status);
        $this->assertNotNull($this->offer->internal_id);
    }

    /** @test */
    public function it_cannot_send_non_draft_offer(): void
    {
        $this->offer->update(['status' => OfferStatus::PENDING]);
        $originalStatus = $this->offer->status;

        $this->service->send();

        $this->offer->refresh();
        $this->assertEquals($originalStatus, $this->offer->status);
    }

    /** @test */
    public function it_can_reject_offer(): void
    {
        $this->service->reject();

        $this->offer->refresh();
        $this->assertEquals(OfferStatus::REJECTED, $this->offer->status);
    }

    /** @test */
    public function it_can_cancel_offer(): void
    {
        $this->service->cancel();

        $this->offer->refresh();
        $this->assertEquals(OfferStatus::CANCELLED, $this->offer->status);
    }

    /** @test */
    public function it_generates_unique_internal_id(): void
    {
        $internalId = $this->service->generateInternalId();

        $this->assertIsString($internalId);
        $this->assertNotEmpty($internalId);
        $this->assertStringContainsString(config('offer.number_prefix'), $internalId);
        $this->assertStringContainsString(today()->year, $internalId);
    }

    /** @test */
    public function it_returns_empty_array_for_non_accepted_offer_to_order(): void
    {
        $orderData = $this->service->toOrder();

        $this->assertIsArray($orderData);
        $this->assertEmpty($orderData);
    }

    /** @test */
    public function it_returns_order_data_for_accepted_offer(): void
    {
        // Create an addressbook entry for the recipient
        $addressbook = \App\Models\Addressbook::factory()->create();

        $this->offer->update([
            'status' => OfferStatus::ACCEPTED,
            'description' => 'Test Offer Description',
            'recipientable_id' => $addressbook->id,
            'recipientable_type' => \App\Models\Addressbook::class,
        ]);

        $orderData = $this->service->toOrder();

        $this->assertIsArray($orderData);
        $this->assertArrayHasKey('description', $orderData);
        $this->assertArrayHasKey('order_date', $orderData);
        $this->assertArrayHasKey('contractor_id', $orderData);
        $this->assertEquals('Test Offer Description', $orderData['description']);
        $this->assertEquals($this->company->id, $orderData['contractor_id']);
    }

    /** @test */
    public function it_uses_fallback_description_for_order(): void
    {
        // Create an addressbook entry for the recipient
        $addressbook = \App\Models\Addressbook::factory()->create();

        $this->offer->update([
            'status' => OfferStatus::ACCEPTED,
            'description' => null,
            'internal_id' => 'OFF-2024-001-001',
            'recipientable_id' => $addressbook->id,
            'recipientable_type' => \App\Models\Addressbook::class,
        ]);

        $orderData = $this->service->toOrder();

        $this->assertEquals('Offer #OFF-2024-001-001', $orderData['description']);
    }

    /** @test */
    public function it_can_get_currency_from_company(): void
    {
        $currency = $this->service->currency();

        $this->assertIsString($currency);
        $this->assertNotEmpty($currency);
    }

    /** @test */
    public function it_can_get_storage_path(): void
    {
        $this->offer->update(['internal_id' => 'OFF-2024-001-001']);

        $path = $this->service->storagePath();

        $this->assertStringContainsString('offers/OFF-2024-001-001.pdf', $path);
    }

    /** @test */
    public function it_can_get_time_duration_for_human(): void
    {
        $minutes = 90.5;
        $duration = $this->service->getTimeDurationForHuman($minutes);

        $this->assertIsString($duration);
        $this->assertStringContainsString('hour', $duration);
    }

    /** @test */
    public function it_can_check_if_user_is_participant(): void
    {
        $isParticipant = $this->service->isParticipant($this->user, $this->company);

        $this->assertTrue($isParticipant);
    }

    /** @test */
    public function it_can_check_if_user_is_not_participant(): void
    {
        $otherCompany = Company::factory()->create();
        $isParticipant = $this->service->isParticipant($this->user, $otherCompany);

        $this->assertFalse($isParticipant);
    }
}
