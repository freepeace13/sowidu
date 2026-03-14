<?php

use App\Enums\InvoiceKind;
use App\Models\Invoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->tinyInteger('kind')->default(InvoiceKind::FINAL())->change();
        });

        Invoice::whereNull('kind')->chunk(50, function ($invoices) {
            $invoices->each->update(['kind' => InvoiceKind::FINAL()]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
};
