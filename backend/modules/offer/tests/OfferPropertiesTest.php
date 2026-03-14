<?php

namespace Modules\Offer\tests;

use App\Enums\OfferStatus;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Offer\Models\Offer;
use Modules\Offer\Support\OfferProperties;
use Modules\Offer\Support\OfferTaxProperty;
use Tests\TestCase;

class OfferPropertiesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected Company $company;

    protected Offer $offer;

    protected OfferProperties $properties;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->offer = Offer::factory()->create([
            'company_id' => $this->company->id,
            'status' => OfferStatus::DRAFT,
        ]);
        $this->properties = new OfferProperties($this->offer);
    }

    /** @test */
    public function it_can_create_offer_properties_instance(): void
    {
        $this->assertInstanceOf(OfferProperties::class, $this->properties);
    }

    /** @test */
    public function it_can_get_taxes_property(): void
    {
        $taxProperty = $this->properties->taxes();

        $this->assertInstanceOf(OfferTaxProperty::class, $taxProperty);
    }

    /** @test */
    public function it_can_attach_tax_to_offer(): void
    {
        $tax = [
            'id' => 1,
            'name' => 'VAT',
            'rate' => 21.0,
        ];

        $result = $this->properties->taxes()->attach($tax);

        $this->assertTrue($result);
        $this->offer->refresh();
        $this->assertArrayHasKey('taxes', $this->offer->properties->toArray());
        $this->assertCount(1, $this->offer->properties->get('taxes'));
    }

    /** @test */
    public function it_can_attach_multiple_taxes(): void
    {
        $tax1 = [
            'id' => 1,
            'name' => 'VAT',
            'rate' => 21.0,
        ];

        $tax2 = [
            'id' => 2,
            'name' => 'Service Tax',
            'rate' => 5.0,
        ];

        $this->properties->taxes()->attach($tax1);
        $this->properties->taxes()->attach($tax2);

        $this->offer->refresh();
        $this->assertCount(2, $this->offer->properties->get('taxes'));
    }

    /** @test */
    public function it_can_merge_existing_tax(): void
    {
        $originalTax = [
            'id' => 1,
            'name' => 'VAT',
            'rate' => 21.0,
        ];

        $updatedTax = [
            'id' => 1,
            'name' => 'VAT',
            'rate' => 25.0,
            'description' => 'Updated VAT rate',
        ];

        $this->properties->taxes()->attach($originalTax);
        $this->properties->taxes()->attach($updatedTax);

        $this->offer->refresh();
        $taxes = $this->offer->properties->get('taxes');
        $tax = collect($taxes)->firstWhere('id', 1);

        $this->assertEquals(25.0, $tax['rate']);
        $this->assertEquals('Updated VAT rate', $tax['description']);
    }

    /** @test */
    public function it_can_get_all_taxes_as_array(): void
    {
        $tax1 = ['id' => 1, 'name' => 'VAT', 'rate' => 21.0];
        $tax2 = ['id' => 2, 'name' => 'Service Tax', 'rate' => 5.0];

        $this->properties->taxes()->attach($tax1);
        $this->properties->taxes()->attach($tax2);

        $taxes = $this->properties->taxes()->all();

        $this->assertIsArray($taxes);
        $this->assertCount(2, $taxes);
    }

    /** @test */
    public function it_can_get_all_taxes_as_collection(): void
    {
        $tax1 = ['id' => 1, 'name' => 'VAT', 'rate' => 21.0];
        $tax2 = ['id' => 2, 'name' => 'Service Tax', 'rate' => 5.0];

        $this->properties->taxes()->attach($tax1);
        $this->properties->taxes()->attach($tax2);

        $taxes = $this->properties->taxes()->toCollection()->all();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $taxes);
        $this->assertCount(2, $taxes);
    }

    /** @test */
    public function it_validates_tax_has_required_keys(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tax must have name and rate keys.');

        $invalidTax = [
            'name' => 'VAT',
            // Missing 'id' and 'rate' keys
        ];

        $this->properties->taxes()->attach($invalidTax);
    }

    /** @test */
    public function it_handles_empty_taxes_property(): void
    {
        $taxes = $this->properties->taxes()->all();

        $this->assertIsArray($taxes);
        $this->assertEmpty($taxes);
    }

    /** @test */
    public function it_handles_existing_taxes_property(): void
    {
        // Set existing taxes property
        $this->offer->update([
            'properties' => [
                'taxes' => [
                    ['id' => 1, 'name' => 'Existing VAT', 'rate' => 20.0],
                ],
            ],
        ]);

        $newTax = [
            'id' => 2,
            'name' => 'New Tax',
            'rate' => 10.0,
        ];

        $result = $this->properties->taxes()->attach($newTax);

        $this->assertTrue($result);
        $this->offer->refresh();
        $this->assertCount(2, $this->offer->properties->get('taxes'));
    }

    /** @test */
    public function it_can_chain_to_collection_method(): void
    {
        $tax = ['id' => 1, 'name' => 'VAT', 'rate' => 21.0];
        $this->properties->taxes()->attach($tax);

        $taxes = $this->properties->taxes()->toCollection()->all();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $taxes);
        $this->assertCount(1, $taxes);
    }
}
