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
        Schema::table('catalog_items', function (Blueprint $table) {
            $table->dropColumn('long_description');
            $table->renameColumn('short_description', 'description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_items', function (Blueprint $table) {
            $table->text('long_description')->nullable();
            $table->renameColumn('description', 'short_description');
        });
    }
};
