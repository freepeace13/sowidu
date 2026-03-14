<?php

namespace Modules\Invoicify\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoicify\Models\InvoiceManualItem;
use Tests\TestCase;

class InvoiceManualItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_manual_item_can_be_created()
    {
        $user = \App\Models\User::factory()->create();
        $company = \App\Models\Company::factory()->create();

        $manualItem = InvoiceManualItem::factory()->create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'name' => 'Test Manual Item',
        ]);

        $this->assertInstanceOf(InvoiceManualItem::class, $manualItem);
        $this->assertEquals('Test Manual Item', $manualItem->name);
        $this->assertEquals($user->id, $manualItem->user_id);
        $this->assertEquals($company->id, $manualItem->company_id);
    }

    public function test_invoice_manual_item_belongs_to_user()
    {
        $user = \App\Models\User::factory()->create();
        $manualItem = InvoiceManualItem::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(\App\Models\User::class, $manualItem->user);
        $this->assertEquals($user->id, $manualItem->user->id);
    }

    public function test_invoice_manual_item_belongs_to_company()
    {
        $company = \App\Models\Company::factory()->create();
        $manualItem = InvoiceManualItem::factory()->create([
            'company_id' => $company->id,
        ]);

        $this->assertInstanceOf(\App\Models\Company::class, $manualItem->company);
        $this->assertEquals($company->id, $manualItem->company->id);
    }
}
