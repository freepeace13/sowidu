<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('company_id');
            $table->foreignId('catalog_item_type_id');
            $table->foreignId('media_id');
            $table->string('name');
            $table->string('internal_id');
            $table->string('vendor_id')->nullable();
            $table->string('manufacture_id')->nullable();
            $table->integer('unit')->nullable();
            $table->double('purchasing_price')->nullable();
            $table->double('selling_price')->nullable();
            $table->string('short_description');
            $table->text('long_description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalog_items');
    }
};
