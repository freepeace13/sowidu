<?php

namespace Modules\Offer\tests;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Offer\Models\Offer;
use Modules\Offer\Models\OfferItem;
use Modules\Offer\Support\OfferCalculationService;
use Tests\TestCase;

class OfferCalculationServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected Company $company;

    protected Offer $offer;

    protected OfferCalculationService $calculationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->offer = Offer::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->calculationService = new OfferCalculationService($this->offer);
    }

    /** @test */
    public function it_can_calculate_subtotal_with_multiple_items(): void
    {
        // Create offer items
        $item1 = OfferItem::factory()->forOffer($this->offer)->create([
            'price' => 100.00,
            'quantity' => 2,
        ]);

        $item2 = OfferItem::factory()->forOffer($this->offer)->create([
            'price' => 50.00,
            'quantity' => 3,
        ]);

        // Refresh the offer to load the items
        $this->offer->refresh();
        $this->offer->load('items');

        // Create a new calculation service instance
        $calculationService = new OfferCalculationService($this->offer);

        $subtotal = $calculationService->subtotal();

        $this->assertEquals(350.00, $subtotal);
    }

    /** @test */
    public function it_returns_zero_subtotal_for_empty_offer(): void
    {
        $subtotal = $this->calculationService->subtotal();

        $this->assertEquals(0.00, $subtotal);
    }

    /** @test */
    public function it_can_calculate_net_total(): void
    {
        OfferItem::factory()->forOffer($this->offer)->create([
            'price' => 100.00,
            'quantity' => 2,
        ]);

        $this->offer->refresh();
        $this->offer->load('items');
        $calculationService = new OfferCalculationService($this->offer);

        $netTotal = $calculationService->netTotal();

        $this->assertEquals(200.00, $netTotal);
    }

    /** @test */
    public function it_can_apply_tax_rate(): void
    {
        $amount = 100.00;
        $rate = 21.0;

        $taxAmount = $this->calculationService->applyTaxRate($amount, $rate);

        $this->assertEquals(21.00, $taxAmount);
    }

    /** @test */
    public function it_can_calculate_total_taxes(): void
    {
        // Add items to offer
        OfferItem::factory()->forOffer($this->offer)->create([
            'price' => 100.00,
            'quantity' => 1,
        ]);

        // Add tax properties to offer
        $this->offer->properties()->taxes()->attach([
            'name' => 'VAT',
            'rate' => 21.0,
            'id' => 1,
        ]);

        $this->offer->properties()->taxes()->attach([
            'name' => 'Service Tax',
            'rate' => 5.0,
            'id' => 2,
        ]);

        $this->offer->refresh();
        $this->offer->load('items');
        $calculationService = new OfferCalculationService($this->offer);

        $totalTaxes = $calculationService->totalTaxes();

        $this->assertEquals(26.00, $totalTaxes);
    }

    /** @test */
    public function it_returns_zero_taxes_when_no_taxes_configured(): void
    {
        OfferItem::factory()->forOffer($this->offer)->create([
            'price' => 100.00,
            'quantity' => 1,
        ]);

        $this->offer->refresh();
        $this->offer->load('items');
        $calculationService = new OfferCalculationService($this->offer);

        $totalTaxes = $calculationService->totalTaxes();

        $this->assertEquals(0.00, $totalTaxes);
    }

    /** @test */
    public function it_can_calculate_grand_total(): void
    {
        // Add items
        OfferItem::factory()->forOffer($this->offer)->create([
            'price' => 100.00,
            'quantity' => 2,
        ]);

        // Add taxes
        $this->offer->properties()->taxes()->attach([
            'name' => 'VAT',
            'rate' => 21.0,
            'id' => 1,
        ]);

        $this->offer->refresh();
        $this->offer->load('items');
        $calculationService = new OfferCalculationService($this->offer);

        $grandTotal = $calculationService->grandTotal();

        $this->assertEquals(242.00, $grandTotal); // 200 + 42 (21% of 200)
    }

    /** @test */
    public function it_can_calculate_total_vats(): void
    {
        OfferItem::factory()->forOffer($this->offer)->create([
            'price' => 100.00,
            'quantity' => 1,
        ]);

        $this->offer->properties()->taxes()->attach([
            'name' => 'VAT',
            'rate' => 21.0,
            'id' => 1,
        ]);

        $this->offer->refresh();
        $this->offer->load('items');
        $calculationService = new OfferCalculationService($this->offer);

        $totalVats = $calculationService->totalVats();

        $this->assertEquals(21.00, $totalVats);
    }

    /** @test */
    public function it_rounds_calculations_correctly(): void
    {
        OfferItem::factory()->forOffer($this->offer)->create([
            'price' => 33.33,
            'quantity' => 3,
        ]);

        $this->offer->refresh();
        $this->offer->load('items');
        $calculationService = new OfferCalculationService($this->offer);

        $subtotal = $calculationService->subtotal();

        $this->assertEquals(99.99, $subtotal);
    }

    /** @test */
    public function it_handles_zero_quantity_items(): void
    {
        OfferItem::factory()->forOffer($this->offer)->create([
            'price' => 100.00,
            'quantity' => 0,
        ]);

        $this->offer->refresh();
        $this->offer->load('items');
        $calculationService = new OfferCalculationService($this->offer);

        $subtotal = $calculationService->subtotal();

        $this->assertEquals(0.00, $subtotal);
    }

    /** @test */
    public function it_handles_negative_prices(): void
    {
        OfferItem::factory()->forOffer($this->offer)->create([
            'price' => -50.00,
            'quantity' => 2,
        ]);

        $this->offer->refresh();
        $this->offer->load('items');
        $calculationService = new OfferCalculationService($this->offer);

        $subtotal = $calculationService->subtotal();

        $this->assertEquals(-100.00, $subtotal);
    }
}
