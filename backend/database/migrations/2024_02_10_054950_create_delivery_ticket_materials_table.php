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
        Schema::create('delivery_ticket_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id');
            $table->foreignId('invoice_id')->nullable();
            $table->double('quantity')->default(1);
            $table->json('details');
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
        Schema::dropIfExists('delivery_ticket_materials');
    }
};
