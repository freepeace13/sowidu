<?php

namespace Modules\Offer\tests;

use App\Enums\OfferStatus;
use App\Enums\OfferType;
use App\Models\Addressbook;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Modules\Offer\Models\Offer;
use Modules\Offer\Repositories\OfferRepository;
use Tests\TestCase;

class OfferRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected Company $company;

    protected OfferRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->repository = OfferRepository::make($this->user, $this->company);
    }

    /** @test */
    public function it_can_create_repository_instance(): void
    {
        $this->assertInstanceOf(OfferRepository::class, $this->repository);
    }

    /** @test */
    public function it_can_create_repository_with_static_make_method(): void
    {
        $repository = OfferRepository::make($this->user, $this->company);

        $this->assertInstanceOf(OfferRepository::class, $repository);
    }

    /** @test */
    public function it_filters_offers_by_company_when_company_provided(): void
    {
        // Create offers for different companies
        $companyOffer = Offer::factory()->create([
            'company_id' => $this->company->id,
            'status' => OfferStatus::PENDING,
        ]);

        $otherCompany = Company::factory()->create();
        $otherCompanyOffer = Offer::factory()->create([
            'company_id' => $otherCompany->id,
            'status' => OfferStatus::PENDING,
        ]);

        $query = $this->repository->getQuery();
        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($companyOffer));
        $this->assertFalse($results->contains($otherCompanyOffer));
    }

    /** @test */
    public function it_filters_private_user_offers_when_no_company_provided(): void
    {
        // Clear cache to ensure fresh data
        Cache::flush();

        // Create addressbook entry for user
        $addressbook = Addressbook::factory()->create([
            'email' => $this->user->email,
        ]);

        // Create offers
        $userOffer = Offer::factory()->create([
            'recipientable_id' => $addressbook->id,
            'recipientable_type' => Addressbook::class,
            'status' => OfferStatus::PENDING,
        ]);

        $draftOffer = Offer::factory()->create([
            'recipientable_id' => $addressbook->id,
            'recipientable_type' => Addressbook::class,
            'status' => OfferStatus::DRAFT,
        ]);

        $otherUserOffer = Offer::factory()->create([
            'status' => OfferStatus::PENDING,
        ]);

        // Create repository and get results
        $repository = OfferRepository::make($this->user, null);
        $query = $repository->getQuery();
        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($userOffer));
        $this->assertFalse($results->contains($draftOffer));
        $this->assertFalse($results->contains($otherUserOffer));
    }

    /** @test */
    public function it_can_filter_by_search_query(): void
    {
        $offer1 = Offer::factory()->create([
            'company_id' => $this->company->id,
            'title' => 'Special Construction Offer',
            'status' => OfferStatus::PENDING,
        ]);

        $offer2 = Offer::factory()->create([
            'company_id' => $this->company->id,
            'title' => 'Regular Maintenance',
            'status' => OfferStatus::PENDING,
        ]);

        $filteredQuery = $this->repository->filter(['q' => 'Construction']);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($offer1));
        $this->assertFalse($results->contains($offer2));
    }

    /** @test */
    public function it_can_filter_by_offer_date(): void
    {
        $today = today();
        $yesterday = today()->subDay();

        $offer1 = Offer::factory()->create([
            'company_id' => $this->company->id,
            'offer_date' => $today,
            'status' => OfferStatus::PENDING,
        ]);

        $offer2 = Offer::factory()->create([
            'company_id' => $this->company->id,
            'offer_date' => $yesterday,
            'status' => OfferStatus::PENDING,
        ]);

        $filteredQuery = $this->repository->filter(['offerDate' => $today->toDateString()]);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($offer1));
        $this->assertFalse($results->contains($offer2));
    }

    /** @test */
    public function it_can_filter_by_status(): void
    {
        $pendingOffer = Offer::factory()->create([
            'company_id' => $this->company->id,
            'status' => OfferStatus::PENDING,
        ]);

        $acceptedOffer = Offer::factory()->create([
            'company_id' => $this->company->id,
            'status' => OfferStatus::ACCEPTED,
        ]);

        $filteredQuery = $this->repository->filter(['status' => OfferStatus::PENDING->value]);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($pendingOffer));
        $this->assertFalse($results->contains($acceptedOffer));
    }

    /** @test */
    public function it_can_filter_by_type(): void
    {
        $incomingOffer = Offer::factory()->create([
            'company_id' => $this->company->id,
            'type' => OfferType::INCOMING,
            'status' => OfferStatus::PENDING,
        ]);

        $outgoingOffer = Offer::factory()->create([
            'company_id' => $this->company->id,
            'type' => OfferType::OUTGOING,
            'status' => OfferStatus::PENDING,
        ]);

        $filteredQuery = $this->repository->filter(['type' => OfferType::INCOMING->value]);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($incomingOffer));
        $this->assertFalse($results->contains($outgoingOffer));
    }

    /** @test */
    public function it_can_combine_multiple_filters(): void
    {
        $targetOffer = Offer::factory()->create([
            'company_id' => $this->company->id,
            'title' => 'Construction Service',
            'status' => OfferStatus::PENDING,
            'type' => OfferType::INCOMING,
            'offer_date' => today(),
        ]);

        $otherOffer = Offer::factory()->create([
            'company_id' => $this->company->id,
            'title' => 'Maintenance Service',
            'status' => OfferStatus::ACCEPTED,
            'type' => OfferType::INCOMING,
            'offer_date' => today(),
        ]);

        $filteredQuery = $this->repository->filter([
            'q' => 'Construction',
            'status' => OfferStatus::PENDING->value,
            'type' => OfferType::INCOMING->value,
            'offerDate' => today()->toDateString(),
        ]);

        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($targetOffer));
        $this->assertFalse($results->contains($otherOffer));
    }

    /** @test */
    public function it_caches_auth_user_recipientable_ids(): void
    {
        Cache::flush();

        $repository = OfferRepository::make($this->user, null);

        // First call should cache the result
        $repository->getQuery();

        $this->assertTrue(Cache::has("auth.user.addressbook.{$this->user->id}.ids"));

        // Second call should use cache
        $repository2 = OfferRepository::make($this->user, null);
        $repository2->getQuery();

        $this->assertTrue(Cache::has("auth.user.addressbook.{$this->user->id}.ids"));
    }

    /** @test */
    public function it_returns_empty_results_when_no_matches(): void
    {
        $filteredQuery = $this->repository->filter([
            'q' => 'NonExistentSearchTerm',
        ]);

        $results = $filteredQuery->get();

        $this->assertCount(0, $results);
    }
}
