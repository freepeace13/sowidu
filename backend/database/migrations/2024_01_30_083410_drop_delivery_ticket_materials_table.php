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
        Schema::dropIfExists('delivery_ticket_materials');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('delivery_ticket_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_item_id');
            $table->foreignId('user_id');
            $table->double('quantity')->default(1);
            $table->json('details');
            $table->timestamps();
        });
    }
};
