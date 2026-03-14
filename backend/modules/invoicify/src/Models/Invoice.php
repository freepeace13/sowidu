<?php

namespace Modules\Invoicify\Models;

use App\Models\Invoice as Model;
use Modules\Invoicify\Enums\InvoiceKind;
use Modules\Invoicify\Enums\InvoiceStatus;
use Modules\Invoicify\Enums\InvoiceType;

/** @todo Will transfer invoice model in this module soon... */
class Invoice extends Model
{
    protected $casts = [
        'delivery_date' => 'datetime:Y-m-d',
        'send_date' => 'datetime:Y-m-d',
        'payment_date' => 'datetime:Y-m-d',
        'execution_period_start' => 'datetime:Y-m-d',
        'execution_period_end' => 'datetime:Y-m-d',
        'type' => InvoiceType::class,
        'biller_details' => 'collection',
        'final_data' => 'collection',
        'status' => InvoiceStatus::class,
        'kind' => InvoiceKind::class,
        'preview_layout' => 'collection',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Invoicify\Database\Factories\InvoiceFactory::new();
    }
}
