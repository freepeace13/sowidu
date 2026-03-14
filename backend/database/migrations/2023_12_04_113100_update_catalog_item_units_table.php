<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('catalog_item_units', function (Blueprint $table) {
            $table->dropColumn('unit');
            $table->string('name')->after('id');
        });
    }

    public function down()
    {
        Schema::table('catalog_item_units', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->tinyInteger('unit');
        });
    }
};
