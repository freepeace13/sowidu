<?php

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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->unsignedInteger('company_id');
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('biller_id');
            $table->foreignId('delivery_address_id');
            $table->foreignId('delivery_date');
            $table->tinyInteger('type');
            $table->string('internal_id');
            $table->string('external_id');
            $table->json('biller_details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
