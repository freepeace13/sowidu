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
        Schema::create('invoice_manual_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('company_id');
            $table->foreignId('catalog_item_type_id');
            $table->string('name');
            $table->string('internal_id');
            $table->string('vendor_id')->nullable();
            $table->string('unit_name');
            $table->double('purchasing_price')->nullable();
            $table->double('selling_price')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('invoice_manual_items');
    }
};
