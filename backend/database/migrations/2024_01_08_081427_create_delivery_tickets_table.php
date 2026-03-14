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
        Schema::create('delivery_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->foreignId('user_id');
            $table->foreignId('deliverer_id');
            $table->foreignId('order_id');
            $table->foreignId('delivery_address_id');
            $table->timestamp('delivery_date');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_tickets');
    }
};
