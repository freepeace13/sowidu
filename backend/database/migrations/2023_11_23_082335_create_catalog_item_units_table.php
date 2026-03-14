<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('catalog_item_units', function (Blueprint $table) {
            $table->id();
            $table->integer('unit');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalog_item_units');
    }
};
